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
            $table->foreignId('kanban_id')->nullable();
            $table->string('scanned_kanban');
            $table->boolean('valid_kanban')->default(false);   // apakah kanban aktif
            $table->boolean('valid_area')->default(false);   // apakah area valid
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
