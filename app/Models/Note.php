<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'semester',
        'interros',
        'devoir1',
        'devoir2',
        'ratio_id',
        'subject_id',
        'recording_id',
    ];

    // Cast du champ JSON en tableau PHP
    protected $casts = [
        'interros' => 'array',
    ];

    /**
     * Relation avec l'enregistrement (élève + classe + année)
     */
    public function recording()
    {
        return $this->belongsTo(Recording::class);
    }

    /**
     * Relation avec la matière
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relation avec le coefficient (ratio)
     */
    public function ratio()
    {
        return $this->belongsTo(Ratio::class);
    }

}
