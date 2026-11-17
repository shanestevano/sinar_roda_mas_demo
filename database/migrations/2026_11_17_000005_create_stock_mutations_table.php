<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tire_id')->constrained('tires')->cascadeOnDelete();
            $table->string('type', 30); // inbound, sale, adjustment, void_return
            $table->integer('quantity'); // positive for addition, negative for reduction
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('reference_type', 50)->nullable(); // Inbound, Transaction, StockOpname
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tire_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_mutations');
    }
};
