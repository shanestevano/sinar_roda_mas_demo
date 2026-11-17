<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tires', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('barcode')->unique()->nullable();
            $table->string('brand'); // Bridgestone, Michelin, Dunlop, GT Radial, dll
            $table->string('pattern'); // Ecopia EP150, Primacy 4, dll
            $table->unsignedSmallInteger('width')->nullable(); // 185, 195, 205
            $table->unsignedSmallInteger('aspect_ratio')->nullable(); // 60, 65, 70
            $table->unsignedSmallInteger('rim_size')->nullable(); // 14, 15, 16, 17, 18
            $table->string('size_display'); // 185/65 R15
            $table->string('type')->default('Tubeless / Passenger'); // Tubeless, SUV / AT, Niaga / Truk
            $table->string('dot_code', 10)->nullable(); // e.g. 2423 (Minggu 24, Tahun 2023)
            $table->unsignedSmallInteger('dot_year')->nullable(); // 2023
            $table->decimal('cost_price', 14, 2)->default(0); // Harga Modal
            $table->decimal('selling_price', 14, 2)->default(0); // Harga Jual
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(5);
            $table->foreignId('rack_location_id')->nullable()->constrained('rack_locations')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index for smart search
            $table->index(['brand', 'size_display']);
            $table->index('dot_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tires');
    }
};
