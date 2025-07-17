<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recording extends Model
{
    protected $fillable = ['student_id', 'year_id', 'classroom_id'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function classroom()
    {
        return $this->belongsTo(PromotionClassroom::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
