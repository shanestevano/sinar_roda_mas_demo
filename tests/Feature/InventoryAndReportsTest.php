<?php

namespace Tests\Feature;

use App\Models\RackLocation;
use App\Models\Supplier;
use App\Models\Tire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryAndReportsTest extends TestCase
{
    use RefreshDatabase;

    private User $gudang;
    private User $owner;
    private User $kasir;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gudang = User::factory()->create(['role' => 'gudang', 'is_active' => true]);
        $this->owner = User::factory()->create(['role' => 'owner', 'is_active' => true]);
        $this->kasir = User::factory()->create(['role' => 'kasir', 'is_active' => true]);
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    public function test_gudang_can_create_tire(): void
    {
        $rack = RackLocation::create([
            'code' => 'RAK-B1',
            'zone' => 'Zona Tengah',
            'capacity' => 60,
        ]);

        $response = $this->actingAs($this->gudang)->post('/products', [
            'sku' => 'TIR-MC-195-65-R15',
            'brand' => 'Michelin',
            'pattern' => 'Energy XM2+',
            'width' => 195,
            'aspect_ratio' => 65,
            'rim_size' => 15,
            'size_display' => '195/65 R15',
            'type' => 'Tubeless / Passenger Car',
            'dot_code' => '1024',
            'dot_year' => 2024,
            'cost_price' => 850000,
            'selling_price' => 1050000,
            'stock' => 12,
            'min_stock' => 4,
            'rack_location_id' => $rack->id,
            'notes' => 'Ban hemat BBM',
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('tires', [
            'sku' => 'TIR-MC-195-65-R15',
            'brand' => 'Michelin',
            'stock' => 12,
        ]);
    }

    public function test_inbound_shipment_increases_tire_stock(): void
    {
        $supplier = Supplier::create([
            'name' => 'PT Dunlop Distribution Indonesia',
            'phone' => '021-99887766',
        ]);

        $tire = Tire::create([
            'sku' => 'TIR-DL-001',
            'brand' => 'Dunlop',
            'pattern' => 'SP Sport LM705',
            'size_display' => '195/65 R15',
            'type' => 'Tubeless',
            'cost_price' => 600000,
            'selling_price' => 750000,
            'stock' => 5,
            'min_stock' => 2,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->gudang)->post('/inbounds', [
            'supplier_id' => $supplier->id,
            'supplier_invoice_number' => 'SJ-DNL-9901',
            'received_date' => date('Y-m-d'),
            'items' => [
                [
                    'tire_id' => $tire->id,
                    'quantity' => 15,
                    'cost_price' => 620000,
                    'dot_code' => '4024',
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $tire->refresh();

        // 5 initial + 15 inbound = 20
        $this->assertEquals(20, $tire->stock);
        $this->assertEquals(620000, (float) $tire->cost_price);

        $this->assertDatabaseHas('stock_mutations', [
            'tire_id' => $tire->id,
            'type' => 'inbound',
            'quantity' => 15,
            'stock_before' => 5,
            'stock_after' => 20,
        ]);
    }

    public function test_barcode_label_view_renders_svg(): void
    {
        $tire = Tire::create([
            'sku' => 'TIR-BARCODE-01',
            'barcode' => '899123456789',
            'brand' => 'Goodyear',
            'pattern' => 'Eagle F1',
            'size_display' => '225/45 R17',
            'type' => 'Tubeless',
            'cost_price' => 1200000,
            'selling_price' => 1500000,
            'stock' => 8,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->gudang)->get("/products/{$tire->id}/barcode");

        $response->assertStatus(200);
        $response->assertSee('<svg', false);
        $response->assertSee('899123456789');
    }

    public function test_owner_can_access_reports_and_csv_export(): void
    {
        $response = $this->actingAs($this->owner)->get('/reports/sales');
        $response->assertStatus(200);

        // Test CSV export stream
        $csvResponse = $this->actingAs($this->owner)->get('/reports/sales?export=csv');
        $csvResponse->assertStatus(200);
        $this->assertTrue(str_contains($csvResponse->headers->get('content-type'), 'text/csv'));
    }

    public function test_dead_stock_report_detects_aged_dot(): void
    {
        Tire::create([
            'sku' => 'TIR-OLD-2021',
            'brand' => 'Bridgestone',
            'pattern' => 'Potenza',
            'size_display' => '205/50 R16',
            'type' => 'Tubeless',
            'dot_code' => '1021',
            'dot_year' => 2021, // Over 3 years old!
            'cost_price' => 900000,
            'selling_price' => 1100000,
            'stock' => 4,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->owner)->get('/reports/dead-stock?years=2');

        $response->assertStatus(200);
        $response->assertSee('TIR-OLD-2021');
        $response->assertSee('Clearance Sale / Diskon 20%');
    }

    public function test_role_middleware_blocks_unauthorized_access(): void
    {
        // Kasir cannot access admin settings
        $response = $this->actingAs($this->kasir)->get('/settings');
        $response->assertStatus(403);

        // Gudang cannot access financial sales reports
        $response = $this->actingAs($this->gudang)->get('/reports/sales');
        $response->assertStatus(403);
    }
}
