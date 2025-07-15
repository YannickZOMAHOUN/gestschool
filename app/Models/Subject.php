<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Ratios associés (coefficients par classe)
     */
    public function ratios()
    {
        return $this->hasMany(Ratio::class);
    }

    /**
     * Notes associées à cette matière
     */
    public function notes()
    {
        //return $this->hasMany(Note::class);
    }

    /**
     * Promotions auxquelles cette matière est affectée
     */
    public function promotionSubjects()
    {
        return $this->hasMany(PromotionSubject::class);
    }

    public function classrooms()
    {
        //return $this->belongsToMany(Classroom::class, 'ratios', 'subject_id', 'classroom_id');
    }

}
