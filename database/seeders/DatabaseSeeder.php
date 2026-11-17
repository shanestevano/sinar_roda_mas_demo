<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\CashierShift;
use App\Models\Inbound;
use App\Models\InboundItem;
use App\Models\RackLocation;
use App\Models\Setting;
use App\Models\StockMutation;
use App\Models\Supplier;
use App\Models\Tire;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Settings
        $settings = [
            ['key' => 'store_name', 'value' => 'Sinar Roda Mas', 'label' => 'Nama Toko / Perusahaan', 'type' => 'text'],
            ['key' => 'store_tagline', 'value' => 'Pusat Penjualan Ban Mobil, Spooring & Balancing', 'label' => 'Slogan Toko', 'type' => 'text'],
            ['key' => 'store_address', 'value' => 'Jl. Panjang Otomotif No. 88, Kebon Jeruk, Jakarta Barat', 'label' => 'Alamat Toko', 'type' => 'textarea'],
            ['key' => 'store_phone', 'value' => '021-5829988 / WhatsApp 0812-9988-7766', 'label' => 'Nomor Telepon / WA', 'type' => 'text'],
            ['key' => 'receipt_footer', 'value' => 'Terima kasih atas kepercayaan Anda di Sinar Roda Mas!\nBan bergaransi pabrikan resmi. Gratis cek angin nitrogen 30 hari.', 'label' => 'Catatan Kaki Struk', 'type' => 'textarea'],
            ['key' => 'default_void_pin', 'value' => '123456', 'label' => 'PIN Keamanan Void Kasir', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // 2. Users (Admin, Owner, Gudang, Kasir)
        $admin = User::firstOrCreate(
            ['email' => 'admin@sinarrodamas.com'],
            [
                'name' => 'Administrator IT',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081111111111',
                'is_active' => true,
                'void_pin' => '123456',
            ]
        );

        $owner = User::firstOrCreate(
            ['email' => 'owner@sinarrodamas.com'],
            [
                'name' => 'Bambang Hartono (Owner)',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'phone' => '081222222222',
                'is_active' => true,
                'void_pin' => '123456',
            ]
        );

        $gudang = User::firstOrCreate(
            ['email' => 'gudang@sinarrodamas.com'],
            [
                'name' => 'Surya Saputra (Staf Gudang)',
                'password' => Hash::make('password'),
                'role' => 'gudang',
                'phone' => '081333333333',
                'is_active' => true,
            ]
        );

        $kasir = User::firstOrCreate(
            ['email' => 'kasir@sinarrodamas.com'],
            [
                'name' => 'Siti Rahma (Kasir Utama)',
                'password' => Hash::make('password'),
                'role' => 'kasir',
                'phone' => '081444444444',
                'is_active' => true,
                'void_pin' => '123456',
            ]
        );

        // 3. Rack Locations
        $racksData = [
            ['code' => 'RAK-A1', 'zone' => 'Zona Depan', 'description' => 'Rak Display Ban Sedan & City Car Ring 14-15', 'capacity' => 60],
            ['code' => 'RAK-A2', 'zone' => 'Zona Depan', 'description' => 'Rak Display Ban MPV Ring 15-16', 'capacity' => 60],
            ['code' => 'RAK-B1', 'zone' => 'Zona Tengah', 'description' => 'Rak Ban SUV & All-Terrain Ring 17-18', 'capacity' => 45],
            ['code' => 'RAK-B2', 'zone' => 'Zona Tengah', 'description' => 'Rak Ban Luxury & Performance Ring 17-19', 'capacity' => 40],
            ['code' => 'RAK-C1', 'zone' => 'Zona Belakang', 'description' => 'Rak Ban Komersial / Truk Ringan (L300/Gran Max/Elf)', 'capacity' => 80],
            ['code' => 'RAK-C2', 'zone' => 'Zona Belakang', 'description' => 'Stok Cadangan Buffer Gudang Dalam', 'capacity' => 120],
        ];

        $racks = [];
        foreach ($racksData as $r) {
            $racks[$r['code']] = RackLocation::firstOrCreate(['code' => $r['code']], $r);
        }

        // 4. Suppliers
        $suppliersData = [
            [
                'name' => 'PT Bridgestone Tire Indonesia Distributor',
                'contact_person' => 'Bpk. Hendra Gunawan',
                'phone' => '021-8901234',
                'email' => 'hendra@bridgestone-dist.co.id',
                'address' => 'Kawasan Industri Pulogadung Blok B No. 12, Jakarta Timur',
            ],
            [
                'name' => 'PT Gajah Tunggal Tbk (GT Radial)',
                'contact_person' => 'Ibu Ratna Dewi',
                'phone' => '021-3865432',
                'email' => 'ratna@gt-tires.co.id',
                'address' => 'Jl. Hayam Wuruk No. 8, Jakarta Pusat',
            ],
            [
                'name' => 'PT Michelin Indonesia Distribution',
                'contact_person' => 'Bpk. Kevin Wijaya',
                'phone' => '021-7896543',
                'email' => 'kevin@michelin-dist.co.id',
                'address' => 'Pondok Indah Office Tower 2 Lt. 10, Jakarta Selatan',
            ],
            [
                'name' => 'PT Goodyear Indonesia Tbk',
                'contact_person' => 'Bpk. David Pranoto',
                'phone' => '0251-8332111',
                'email' => 'sales@goodyear-bogor.com',
                'address' => 'Jl. Pemuda No. 27, Tanah Sareal, Kota Bogor',
            ],
        ];

        $suppliers = [];
        foreach ($suppliersData as $s) {
            $suppliers[] = Supplier::firstOrCreate(['name' => $s['name']], $s);
        }

        // 5. Tires Data
        $tiresData = [
            [
                'sku' => 'TIR-BS-185-65-R15',
                'barcode' => '899100100150',
                'brand' => 'Bridgestone',
                'pattern' => 'Ecopia EP150',
                'width' => 185,
                'aspect_ratio' => 65,
                'rim_size' => 15,
                'size_display' => '185/65 R15',
                'type' => 'Tubeless / Passenger Car',
                'dot_code' => '3224', // Week 32 Year 2024
                'dot_year' => 2024,
                'cost_price' => 720000,
                'selling_price' => 875000,
                'stock' => 28,
                'min_stock' => 6,
                'rack_location_id' => $racks['RAK-A2']->id,
                'notes' => 'Cocok untuk Toyota Avanza, Daihatsu Xenia, Honda Mobilio.',
            ],
            [
                'sku' => 'TIR-DL-185-65-R15',
                'barcode' => '899100100300',
                'brand' => 'Dunlop',
                'pattern' => 'Enasave EC300+',
                'width' => 185,
                'aspect_ratio' => 65,
                'rim_size' => 15,
                'size_display' => '185/65 R15',
                'type' => 'Tubeless / Passenger Car',
                'dot_code' => '1824',
                'dot_year' => 2024,
                'cost_price' => 650000,
                'selling_price' => 790000,
                'stock' => 18,
                'min_stock' => 5,
                'rack_location_id' => $racks['RAK-A2']->id,
                'notes' => 'Standar Suzuki Ertiga, Nissan Grand Livina.',
            ],
            [
                'sku' => 'TIR-MC-205-55-R16',
                'barcode' => '899100100416',
                'brand' => 'Michelin',
                'pattern' => 'Primacy 4',
                'width' => 205,
                'aspect_ratio' => 55,
                'rim_size' => 16,
                'size_display' => '205/55 R16',
                'type' => 'Tubeless / Premium Passenger',
                'dot_code' => '4223',
                'dot_year' => 2023,
                'cost_price' => 1150000,
                'selling_price' => 1420000,
                'stock' => 16,
                'min_stock' => 4,
                'rack_location_id' => $racks['RAK-B2']->id,
                'notes' => 'Sangat senyap dan grip basah maksimal. Toyota Corolla Altis, Honda Civic.',
            ],
            [
                'sku' => 'TIR-GT-175-65-R14',
                'barcode' => '899100100140',
                'brand' => 'GT Radial',
                'pattern' => 'Champiro Eco',
                'width' => 175,
                'aspect_ratio' => 65,
                'rim_size' => 14,
                'size_display' => '175/65 R14',
                'type' => 'Tubeless / City Car',
                'dot_code' => '2024',
                'dot_year' => 2024,
                'cost_price' => 480000,
                'selling_price' => 590000,
                'stock' => 32,
                'min_stock' => 8,
                'rack_location_id' => $racks['RAK-A1']->id,
                'notes' => 'Paling laris untuk LCGC: Honda Brio, Toyota Agya, Daihatsu Calya/Sigra.',
            ],
            [
                'sku' => 'TIR-BS-265-65-R17',
                'barcode' => '899100100697',
                'brand' => 'Bridgestone',
                'pattern' => 'Dueler A/T 697',
                'width' => 265,
                'aspect_ratio' => 65,
                'rim_size' => 17,
                'size_display' => '265/65 R17',
                'type' => 'SUV / All-Terrain',
                'dot_code' => '1524',
                'dot_year' => 2024,
                'cost_price' => 1680000,
                'selling_price' => 2050000,
                'stock' => 12,
                'min_stock' => 4,
                'rack_location_id' => $racks['RAK-B1']->id,
                'notes' => 'Toyota Fortuner, Mitsubishi Pajero Sport. Kuat segala medan.',
            ],
            [
                'sku' => 'TIR-GY-195-60-R16',
                'barcode' => '899100100195',
                'brand' => 'Goodyear',
                'pattern' => 'Assurance TripleMax 2',
                'width' => 195,
                'aspect_ratio' => 60,
                'rim_size' => 16,
                'size_display' => '195/60 R16',
                'type' => 'Tubeless / Passenger Car',
                'dot_code' => '2824',
                'dot_year' => 2024,
                'cost_price' => 810000,
                'selling_price' => 985000,
                'stock' => 15,
                'min_stock' => 4,
                'rack_location_id' => $racks['RAK-A2']->id,
                'notes' => 'Pengereman optimal di jalan basah. Toyota Rush, Daihatsu Terios, Mitsubishi Xpander.',
            ],
            [
                'sku' => 'TIR-BS-215-55-R17',
                'barcode' => '899100100215',
                'brand' => 'Bridgestone',
                'pattern' => 'Turanza T005A',
                'width' => 215,
                'aspect_ratio' => 55,
                'rim_size' => 17,
                'size_display' => '215/55 R17',
                'type' => 'Tubeless / Luxury Comfort',
                'dot_code' => '1024',
                'dot_year' => 2024,
                'cost_price' => 1350000,
                'selling_price' => 1690000,
                'stock' => 8,
                'min_stock' => 4,
                'rack_location_id' => $racks['RAK-B2']->id,
                'notes' => 'Kenyamanan prima untuk Toyota Camry, Honda Accord, Nissan Teana.',
            ],
            [
                'sku' => 'TIR-GT-195-R14C',
                'barcode' => '899100100194',
                'brand' => 'GT Radial',
                'pattern' => 'Maxmiler Pro 8PR',
                'width' => 195,
                'aspect_ratio' => 80,
                'rim_size' => 14,
                'size_display' => '195 R14C 8PR',
                'type' => 'Commercial / Niaga Truk Ringan',
                'dot_code' => '0524',
                'dot_year' => 2024,
                'cost_price' => 690000,
                'selling_price' => 820000,
                'stock' => 22,
                'min_stock' => 6,
                'rack_location_id' => $racks['RAK-C1']->id,
                'notes' => 'Ban muatan berat untuk Mitsubishi L300, Daihatsu Gran Max Pick Up.',
            ],
            // Sample Aged/Old DOT (for dead stock warning)
            [
                'sku' => 'TIR-HK-185-70-R14',
                'barcode' => '899100100185',
                'brand' => 'Hankook',
                'pattern' => 'Kinergy Eco2',
                'width' => 185,
                'aspect_ratio' => 70,
                'rim_size' => 14,
                'size_display' => '185/70 R14',
                'type' => 'Tubeless / Passenger Car',
                'dot_code' => '4421', // Year 2021 (Dead stock alert!)
                'dot_year' => 2021,
                'cost_price' => 520000,
                'selling_price' => 625000,
                'stock' => 4,
                'min_stock' => 5,
                'rack_location_id' => $racks['RAK-A1']->id,
                'notes' => 'Stok lama (DOT 2021). Perlu promo diskon khusus atau clearance sale.',
            ],
            // Low stock example
            [
                'sku' => 'TIR-MC-225-60-R18',
                'barcode' => '899100100225',
                'brand' => 'Michelin',
                'pattern' => 'Pilot Sport 4 SUV',
                'width' => 225,
                'aspect_ratio' => 60,
                'rim_size' => 18,
                'size_display' => '225/60 R18',
                'type' => 'SUV / Performance',
                'dot_code' => '1224',
                'dot_year' => 2024,
                'cost_price' => 1950000,
                'selling_price' => 2450000,
                'stock' => 2, // Low stock (< min_stock 4)
                'min_stock' => 4,
                'rack_location_id' => $racks['RAK-B1']->id,
                'notes' => 'Stok menipis! Segera re-order ke supplier.',
            ],
        ];

        $createdTires = [];
        foreach ($tiresData as $tData) {
            $createdTires[] = Tire::firstOrCreate(['sku' => $tData['sku']], $tData);
        }

        // 6. Inbound sample record
        $inbound = Inbound::firstOrCreate(
            ['reference_number' => 'INB-20261110-001'],
            [
                'supplier_id' => $suppliers[0]->id,
                'supplier_invoice_number' => 'SJ-BS-88992',
                'received_date' => Carbon::now()->subDays(6)->toDateString(),
                'user_id' => $gudang->id,
                'total_amount' => 14400000,
                'notes' => 'Penerimaan rutin pasokan ban Bridgestone Ecopia.',
            ]
        );

        InboundItem::firstOrCreate(
            ['inbound_id' => $inbound->id, 'tire_id' => $createdTires[0]->id],
            [
                'quantity' => 20,
                'cost_price' => 720000,
                'subtotal' => 14400000,
                'dot_code' => '3224',
            ]
        );

        StockMutation::firstOrCreate(
            ['reference_type' => 'Inbound', 'reference_id' => $inbound->id],
            [
                'tire_id' => $createdTires[0]->id,
                'type' => 'inbound',
                'quantity' => 20,
                'stock_before' => 8,
                'stock_after' => 28,
                'user_id' => $gudang->id,
                'notes' => 'Penerimaan barang dari PT Bridgestone Tire Indonesia (Ref: ' . $inbound->reference_number . ')',
            ]
        );

        // 7. Cashier Shift & Sample Transactions
        $shift = CashierShift::firstOrCreate(
            ['user_id' => $kasir->id, 'status' => 'open'],
            [
                'shift_date' => Carbon::today()->toDateString(),
                'opened_at' => Carbon::now()->subHours(4),
                'starting_cash' => 500000,
                'expected_cash' => 500000 + 1750000, // starting cash + cash transaction
                'actual_cash' => null,
                'difference' => 0,
                'total_transactions' => 2,
                'total_cash_sales' => 1750000,
                'total_sales' => 3530000,
                'notes' => 'Shift pagi kasir toko.',
            ]
        );

        // Transaction 1: Completed Cash (2x Avanza tires)
        $tx1 = Transaction::firstOrCreate(
            ['invoice_number' => 'INV-20261117-0001'],
            [
                'user_id' => $kasir->id,
                'shift_id' => $shift->id,
                'customer_name' => 'Bpk. Agus Santoso',
                'customer_phone' => '081288776655',
                'vehicle_plate' => 'B 1928 KFA (Avanza)',
                'subtotal' => 1750000,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'grand_total' => 1750000,
                'payment_method' => 'cash',
                'amount_paid' => 2000000,
                'change_amount' => 250000,
                'status' => 'completed',
            ]
        );

        TransactionItem::firstOrCreate(
            ['transaction_id' => $tx1->id, 'tire_id' => $createdTires[0]->id],
            [
                'quantity' => 2,
                'cost_price' => 720000,
                'unit_price' => 875000,
                'subtotal' => 1750000,
            ]
        );

        StockMutation::firstOrCreate(
            ['reference_type' => 'Transaction', 'reference_id' => $tx1->id],
            [
                'tire_id' => $createdTires[0]->id,
                'type' => 'sale',
                'quantity' => -2,
                'stock_before' => 30,
                'stock_after' => 28,
                'user_id' => $kasir->id,
                'notes' => 'Penjualan kasir faktur: ' . $tx1->invoice_number,
            ]
        );

        // Transaction 2: Transfer (GT Radial LCGC tires)
        $tx2 = Transaction::firstOrCreate(
            ['invoice_number' => 'INV-20261117-0002'],
            [
                'user_id' => $kasir->id,
                'shift_id' => $shift->id,
                'customer_name' => 'Ibu Maria Ulfah',
                'customer_phone' => '081599001122',
                'vehicle_plate' => 'B 2341 TZX (Brio)',
                'subtotal' => 1780000,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'grand_total' => 1780000,
                'payment_method' => 'transfer',
                'amount_paid' => 1780000,
                'change_amount' => 0,
                'status' => 'completed',
            ]
        );

        TransactionItem::firstOrCreate(
            ['transaction_id' => $tx2->id, 'tire_id' => $createdTires[3]->id],
            [
                'quantity' => 3,
                'cost_price' => 480000,
                'unit_price' => 593333.33,
                'subtotal' => 1780000,
            ]
        );

        // 8. Log initial activity
        ActivityLog::record('SYSTEM_INIT', 'Inisialisasi database sistem Sinar Roda Mas dengan master data awal.');
    }
}
