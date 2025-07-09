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
        Schema::create('scan_kanbans', function (Blueprint $table) {
            $table->id();
             $table->string('nik');
            $table->foreignId('area_id')->constrained();
            $table->foreignId('kanban_id')->constrained();
            $table->boolean('is_valid')->default(false);   // apakah kanban aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_kanbans');
    }
};
