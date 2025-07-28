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
        // database/migrations/xxxx_create_class_subject_teachers_table.php
        Schema::create('class_subject_teachers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('classroom_id')->constrained('promotion_classrooms')->onDelete('cascade');
        $table->foreignId('subject_id')->constrained()->onDelete('cascade');
        $table->foreignId('year_id')->constrained()->onDelete('cascade');
        $table->boolean('is_principal')->default(false);
        $table->timestamps();
        $table->unique(
            ['user_id', 'classroom_id', 'subject_id', 'year_id'],
            'unique_teacher_class_subject_year'
        );});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_subject_teachers');
    }
};
