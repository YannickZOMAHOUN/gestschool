<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model PrincipalClassTeacher
 *
 * Représente le professeur principal d'une classe pour une année donnée.
 * Un PP peut être un enseignant qui n'enseigne PAS forcément dans cette classe.
 */
class PrincipalClassTeacher extends Model
{
    protected $fillable = [
        'user_id',
        'classroom_id',
        'year_id',
    ];

    /** L'enseignant désigné comme PP */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** La classe concernée */
    public function classroom()
    {
        return $this->belongsTo(PromotionClassroom::class, 'classroom_id');
    }

    /** L'année scolaire */
    public function year()
    {
        return $this->belongsTo(Year::class);
    }
}
