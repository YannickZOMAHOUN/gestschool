<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promotion_subjects', function (Blueprint $table) {
            // null = S1+S2, 1 = S1 seulement, 2 = S2 seulement
            $table->unsignedTinyInteger('semester')
                  ->nullable()
                  ->default(null)
                  ->after('subject_id');
        });

        Schema::table('ratios', function (Blueprint $table) {
            $table->unsignedTinyInteger('semester')
                  ->nullable()
                  ->default(null)
                  ->after('coefficient');
        });
    }

    public function down(): void
    {
        Schema::table('promotion_subjects', function (Blueprint $table) {
            $table->dropColumn('semester');
        });

        Schema::table('ratios', function (Blueprint $table) {
            $table->dropColumn('semester');
        });
    }
};
