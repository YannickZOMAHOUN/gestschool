<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_classrooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('year_id');
            $table->unsignedBigInteger('sector_id');
            $table->unsignedBigInteger('promotion_sector_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('year_id')->references('id')->on('years')->onDelete('cascade');
            $table->foreign('sector_id')->references('id')->on('sectors')->onDelete('cascade');
            $table->foreign('promotion_sector_id')->references('id')->on('promotion_sectors')->onDelete('cascade');

            $table->unique(['year_id', 'sector_id', 'promotion_sector_id', 'name'], 'unique_classroom');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_classrooms');
    }
};
