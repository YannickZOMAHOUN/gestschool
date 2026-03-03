<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('notes', 'is_locked')) {
            Schema::table('notes', function (Blueprint $table) {
                $table->boolean('is_locked')->default(false)->after('devoir2');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('notes', 'is_locked')) {
            Schema::table('notes', function (Blueprint $table) {
                $table->dropColumn('is_locked');
            });
        }
    }
};
