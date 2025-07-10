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
        Schema::create('kanbans', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('kanban_category_id');
            $table->foreignId('area_id');
            $table->string('conveyor')->nullable();
            $table->string('family')->nullable();
            $table->string('issue_number');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanbans');
    }
};
