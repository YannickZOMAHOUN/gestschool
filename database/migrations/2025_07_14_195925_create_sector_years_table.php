<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sector_years', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('year_id');
            $table->unsignedBigInteger('sector_id');
            $table->timestamps();

            $table->foreign('year_id', 'sector_years_year_id_foreign')
                  ->references('id')->on('years')->onDelete('cascade');

            $table->foreign('sector_id', 'sector_years_sector_id_foreign')
                  ->references('id')->on('sectors')->onDelete('cascade');

            $table->unique(['year_id', 'sector_id'], 'unique_year_sector');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sector_years');
    }
};
