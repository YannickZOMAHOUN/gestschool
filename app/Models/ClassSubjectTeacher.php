<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSubjectTeacher extends Model
{
    protected $fillable = [
        'user_id',
        'classroom_id',
        'subject_id',
    ];

    // Enseignant
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Classe
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    // Matière
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
