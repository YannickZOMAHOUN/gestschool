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
        Schema::create('promotion_sectors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sector_year_id'); // 🔄 remplace sector_year_id pour correspondre à la logique de store()
            $table->string('promotion_sector'); // Ex : '2nde IMI'
            $table->timestamps();
            $table->foreign('sector_year_id')->references('id')->on('sector_years')->onDelete('cascade');
            $table->unique(['sector_year_id', 'promotion_sector']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_sectors');
    }
};
