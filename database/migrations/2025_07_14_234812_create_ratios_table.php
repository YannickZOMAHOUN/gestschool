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
        Schema::create('ratios', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('year_id');
        $table->unsignedBigInteger('promotion_sector_id');
        $table->unsignedBigInteger('classroom_id');
        $table->unsignedBigInteger('subject_id');
        $table->integer('coefficient')->default(1);
        $table->unique(['year_id', 'promotion_sector_id', 'classroom_id', 'subject_id'], 'unique_ratio');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratios');
    }
};
