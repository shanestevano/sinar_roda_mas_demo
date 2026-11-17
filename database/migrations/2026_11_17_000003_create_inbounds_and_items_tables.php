<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbounds', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique(); // e.g. INB-20261117-001
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->string('supplier_invoice_number')->nullable(); // No Surat Jalan / No Faktur Supplier
            $table->date('received_date');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Gudang staff
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inbound_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inbound_id')->constrained('inbounds')->cascadeOnDelete();
            $table->foreignId('tire_id')->constrained('tires')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('cost_price', 14, 2);
            $table->decimal('subtotal', 14, 2);
            $table->string('dot_code', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_items');
        Schema::dropIfExists('inbounds');
    }
};
