<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionClassroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'year_id',
        'sector_id',
        'promotion_sector_id',
        'name'
    ];

    // Relations

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function promotionSector()
    {
        return $this->belongsTo(PromotionSector::class);
    }

    public function ratios()
    {
        return $this->hasMany(Ratio::class, 'classroom_id');
    }
}
