<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ratio extends Model
{
    protected $fillable = [
        'year_id',
        'promotion_sector_id',
        'classroom_id',
        'subject_id',
        'coefficient',
    ];

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function promotionSector()
    {
        return $this->belongsTo(PromotionSector::class);
    }

    public function classroom()
    {
        return $this->belongsTo(PromotionClassroom::class);
    }

    /**
     * Toutes les notes associées à ce ratio (optionnel)
     */
    public function notes()
    {
       // return $this->hasMany(Note::class);
    }
}
