<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'name',
        'surname',
        'sex',
        'birthday',
        'birthplace',
        'number',
        'aptitude',
        'user_id',
    ];

    /**
     * Un élève peut avoir plusieurs enregistrements (s'il change de classe ou d'année).
     */
    public function recordings()
    {
        return $this->hasMany(Recording::class);
    }
 
}
