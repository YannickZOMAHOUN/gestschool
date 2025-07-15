<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromotionSubject extends Model
{
    use HasFactory;

    protected $fillable = ['promotion_sector_id', 'subject_id'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function promotion()
    {
        return $this->belongsTo(PromotionSector::class, 'promotion_sector_id');
    }
}
