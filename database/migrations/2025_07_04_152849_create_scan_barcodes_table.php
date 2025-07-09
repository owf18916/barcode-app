<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scan_barcodes', function (Blueprint $table) {
            $table->id();
            $table->string('nik');                         // NIK user yang scan
            $table->foreignId('area_id')->constrained();   // Relasi ke areas
            $table->string('barcode1');
            $table->string('barcode2');
            $table->string('barcode3');
            $table->boolean('is_match')->default(false);   // Apakah hasil scan cocok
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_barcodes');
    }
};
