<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotesTable extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();

            // Liens vers enregistrement (élève) et matière
            $table->foreignId('recording_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('ratio_id')->constrained()->onDelete('cascade');


            // Semestre (1 ou 2)
            $table->unsignedTinyInteger('semester');

            // Notes d'interrogation sous forme de tableau
            $table->json('interros')->nullable();

            // Deux champs pour devoirs
            $table->decimal('devoir1', 5, 2)->nullable();
            $table->decimal('devoir2', 5, 2)->nullable();

            $table->timestamps();

            // Unicité par élève + matière + semestre
            $table->unique(['recording_id', 'subject_id','ratio_id', 'semester'], 'unique_note_per_semester');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
}
