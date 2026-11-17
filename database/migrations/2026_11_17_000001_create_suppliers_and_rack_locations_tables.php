<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('rack_locations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. RAK-A1, RAK-B2
            $table->string('zone'); // e.g. Zona Depan, Zona Ban Penumpang, Zona Ban Truk
            $table->string('description')->nullable();
            $table->integer('capacity')->default(50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rack_locations');
        Schema::dropIfExists('suppliers');
    }
};
