<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('principal_class_teachers', function (Blueprint $table) {
            $table->id();

            // L'enseignant désigné PP (doit avoir le rôle Enseignant)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // La classe concernée
            $table->foreignId('classroom_id')
                  ->constrained('promotion_classrooms')
                  ->onDelete('cascade');

            // L'année scolaire
            $table->foreignId('year_id')
                  ->constrained('years')
                  ->onDelete('cascade');

            $table->timestamps();

            // Un seul PP par classe par année
            $table->unique(['classroom_id', 'year_id'], 'unique_pp_per_class_year');
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('devoir2');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('principal_class_teachers');

        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('is_locked');
        });
    }
};
