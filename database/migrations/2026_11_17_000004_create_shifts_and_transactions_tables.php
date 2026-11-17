<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('shift_date');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('starting_cash', 14, 2)->default(0);
            $table->decimal('expected_cash', 14, 2)->default(0);
            $table->decimal('actual_cash', 14, 2)->nullable();
            $table->decimal('difference', 14, 2)->nullable();
            $table->integer('total_transactions')->default(0);
            $table->decimal('total_cash_sales', 14, 2)->default(0);
            $table->decimal('total_sales', 14, 2)->default(0);
            $table->string('status', 20)->default('open'); // open, closed
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // e.g. INV-20261117-0001
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Kasir
            $table->foreignId('shift_id')->nullable()->constrained('cashier_shifts')->nullOnDelete();
            $table->string('customer_name')->default('Pelanggan Umum');
            $table->string('customer_phone')->nullable();
            $table->string('vehicle_plate')->nullable(); // e.g. B 1234 ABC
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2)->default(0);
            $table->string('payment_method')->default('cash'); // cash, transfer, qris, debit
            $table->decimal('amount_paid', 14, 2)->default(0);
            $table->decimal('change_amount', 14, 2)->default(0);
            $table->string('status', 20)->default('completed'); // completed, voided
            $table->text('void_reason')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('voided_at')->nullable();
            $table->timestamps();

            $table->index('invoice_number');
            $table->index('status');
            $table->index('created_at');
        });

        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('tire_id')->constrained('tires')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('cost_price', 14, 2); // To calculate gross profit accurately
            $table->decimal('unit_price', 14, 2);
            $table->decimal('subtotal', 14, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('cashier_shifts');
    }
};
