<?php

namespace Tests\Feature;

use App\Models\CashierShift;
use App\Models\RackLocation;
use App\Models\Setting;
use App\Models\StockMutation;
use App\Models\Tire;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosAndTransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $cashier;
    private Tire $tire;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set('default_void_pin', '123456');

        $this->cashier = User::factory()->create([
            'role' => 'kasir',
            'is_active' => true,
            'void_pin' => '123456',
        ]);

        $rack = RackLocation::create([
            'code' => 'RAK-T1',
            'zone' => 'Zona Test',
            'capacity' => 50,
        ]);

        $this->tire = Tire::create([
            'sku' => 'TIR-TEST-001',
            'barcode' => '8999990001',
            'brand' => 'Bridgestone',
            'pattern' => 'Ecopia EP150',
            'width' => 185,
            'aspect_ratio' => 65,
            'rim_size' => 15,
            'size_display' => '185/65 R15',
            'type' => 'Tubeless / Passenger Car',
            'dot_code' => '2424',
            'dot_year' => 2024,
            'cost_price' => 700000,
            'selling_price' => 850000,
            'stock' => 10,
            'min_stock' => 2,
            'rack_location_id' => $rack->id,
            'is_active' => true,
        ]);
    }

    public function test_smart_search_finds_tires_instantly(): void
    {
        $response = $this->actingAs($this->cashier)->getJson('/pos/search?q=Ecopia');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'sku' => 'TIR-TEST-001',
            'brand' => 'Bridgestone',
            'size_display' => '185/65 R15',
            'rack_code' => 'RAK-T1',
        ]);
    }

    public function test_pos_checkout_wrapped_in_transaction_mutates_stock(): void
    {
        $payload = [
            'customer_name' => 'Bpk. Ahmad Subandi',
            'vehicle_plate' => 'B 1234 XYZ',
            'payment_method' => 'cash',
            'amount_paid' => 2000000,
            'discount_amount' => 50000,
            'items' => [
                [
                    'tire_id' => $this->tire->id,
                    'quantity' => 2,
                    'unit_price' => 850000,
                ],
            ],
        ];

        $response = $this->actingAs($this->cashier)->postJson('/pos/checkout', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Assert Stock Reduced by 2
        $this->tire->refresh();
        $this->assertEquals(8, $this->tire->stock);

        // Assert Transaction Recorded
        $this->assertDatabaseHas('transactions', [
            'customer_name' => 'Bpk. Ahmad Subandi',
            'vehicle_plate' => 'B 1234 XYZ',
            'subtotal' => 1700000,
            'discount_amount' => 50000,
            'grand_total' => 1650000,
            'amount_paid' => 2000000,
            'change_amount' => 350000,
            'status' => 'completed',
        ]);

        // Assert Stock Mutation Recorded
        $this->assertDatabaseHas('stock_mutations', [
            'tire_id' => $this->tire->id,
            'type' => 'sale',
            'quantity' => -2,
            'stock_before' => 10,
            'stock_after' => 8,
        ]);
    }

    public function test_checkout_fails_gracefully_when_stock_insufficient(): void
    {
        $payload = [
            'customer_name' => 'Pelanggan Borong',
            'payment_method' => 'cash',
            'amount_paid' => 50000000,
            'items' => [
                [
                    'tire_id' => $this->tire->id,
                    'quantity' => 20, // Stock is only 10!
                    'unit_price' => 850000,
                ],
            ],
        ];

        $response = $this->actingAs($this->cashier)->postJson('/pos/checkout', $payload);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'success' => false,
        ]);

        // Stock must remain unchanged (atomic rollback)
        $this->tire->refresh();
        $this->assertEquals(10, $this->tire->stock);
    }

    public function test_void_transaction_with_valid_pin_restores_stock(): void
    {
        // 1. Run checkout first
        $checkoutRes = $this->actingAs($this->cashier)->postJson('/pos/checkout', [
            'customer_name' => 'Pelanggan Void',
            'payment_method' => 'cash',
            'amount_paid' => 1000000,
            'discount_amount' => 0,
            'items' => [
                ['tire_id' => $this->tire->id, 'quantity' => 1, 'unit_price' => 850000],
            ],
        ]);

        $checkoutRes->assertOk();
        $checkoutRes->assertJson(['success' => true]);

        $this->tire->refresh();
        $this->assertEquals(9, $this->tire->stock);

        $tx = Transaction::latest()->first();

        // 2. Void with valid PIN (123456)
        $response = $this->actingAs($this->cashier)->postJson("/pos/void/{$tx->id}", [
            'pin' => '123456',
            'reason' => 'Pelanggan salah memilih ukuran velg',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['success' => true]);

        // Assert transaction marked voided
        $tx->refresh();
        $this->assertEquals('voided', $tx->status);
        $this->assertEquals('Pelanggan salah memilih ukuran velg', $tx->void_reason);

        // Assert stock restored back to 10
        $this->tire->refresh();
        $this->assertEquals(10, $this->tire->stock);

        // Assert void_return stock mutation exists
        $this->assertDatabaseHas('stock_mutations', [
            'tire_id' => $this->tire->id,
            'type' => 'void_return',
            'quantity' => 1,
            'stock_before' => 9,
            'stock_after' => 10,
        ]);
    }

    public function test_void_with_invalid_pin_is_rejected(): void
    {
        $tx = Transaction::create([
            'invoice_number' => 'INV-TEST-0001',
            'user_id' => $this->cashier->id,
            'customer_name' => 'Testing',
            'subtotal' => 850000,
            'grand_total' => 850000,
            'amount_paid' => 850000,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->cashier)->postJson("/pos/void/{$tx->id}", [
            'pin' => '999999', // Wrong PIN
            'reason' => 'Test void salah PIN',
        ]);

        $response->assertStatus(403);
        $tx->refresh();
        $this->assertEquals('completed', $tx->status);
    }
}
