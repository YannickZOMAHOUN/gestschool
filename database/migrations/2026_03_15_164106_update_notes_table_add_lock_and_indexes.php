<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function getIndexes(): array
    {
        return collect(DB::select("SHOW INDEX FROM notes"))
            ->pluck('Key_name')
            ->unique()
            ->toArray();
    }

    private function getForeignKeys(): array
    {
        return collect(DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_NAME = 'notes'
            AND TABLE_SCHEMA = DATABASE()
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        "))->pluck('CONSTRAINT_NAME')->toArray();
    }

    public function up(): void
    {
        $indexes = $this->getIndexes();
        $fks     = $this->getForeignKeys();

        Schema::table('notes', function (Blueprint $table) use ($indexes, $fks) {

            // 1. Supprimer les FKs seulement si elles existent
            if (in_array('notes_recording_id_foreign', $fks)) {
                $table->dropForeign('notes_recording_id_foreign');
            }
            if (in_array('notes_subject_id_foreign', $fks)) {
                $table->dropForeign('notes_subject_id_foreign');
            }
            if (in_array('notes_ratio_id_foreign', $fks)) {
                $table->dropForeign('notes_ratio_id_foreign');
            }

            // 2. Remplacer l'ancienne contrainte unique si elle existe encore
            if (in_array('unique_note_per_semester', $indexes)) {
                $table->dropUnique('unique_note_per_semester');
            }
            if (!in_array('notes_unique_recording_subject_ratio_sem', $indexes)) {
                $table->unique(
                    ['recording_id', 'subject_id', 'ratio_id', 'semester'],
                    'notes_unique_recording_subject_ratio_sem'
                );
            }

            // 3. Remettre les FKs
            $table->foreign('recording_id')->references('id')->on('recordings')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('ratio_id')->references('id')->on('ratios')->onDelete('cascade');

            // 4. Index is_locked
            if (!in_array('notes_is_locked_idx', $indexes)) {
                $table->index('is_locked', 'notes_is_locked_idx');
            }

            // 5. Index composé subject + semester
            if (!in_array('notes_subject_sem_idx', $indexes)) {
                $table->index(['subject_id', 'semester'], 'notes_subject_sem_idx');
            }
        });
    }

    public function down(): void
    {
        $indexes = $this->getIndexes();
        $fks     = $this->getForeignKeys();

        Schema::table('notes', function (Blueprint $table) use ($indexes, $fks) {
            if (in_array('notes_recording_id_foreign', $fks)) {
                $table->dropForeign('notes_recording_id_foreign');
            }
            if (in_array('notes_subject_id_foreign', $fks)) {
                $table->dropForeign('notes_subject_id_foreign');
            }
            if (in_array('notes_ratio_id_foreign', $fks)) {
                $table->dropForeign('notes_ratio_id_foreign');
            }

            if (in_array('notes_subject_sem_idx', $indexes)) {
                $table->dropIndex('notes_subject_sem_idx');
            }
            if (in_array('notes_is_locked_idx', $indexes)) {
                $table->dropIndex('notes_is_locked_idx');
            }
            if (in_array('notes_unique_recording_subject_ratio_sem', $indexes)) {
                $table->dropUnique('notes_unique_recording_subject_ratio_sem');
            }
            if (!in_array('unique_note_per_semester', $indexes)) {
                $table->unique(
                    ['recording_id', 'subject_id', 'ratio_id', 'semester'],
                    'unique_note_per_semester'
                );
            }
            
            $table->foreign('recording_id')->references('id')->on('recordings')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('ratio_id')->references('id')->on('ratios')->onDelete('cascade');
        });
    }
};
