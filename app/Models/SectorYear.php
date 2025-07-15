<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SectorYear extends Model
{
    use HasFactory;

    protected $fillable = ['year_id', 'sector_id'];

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function promotionSectors()
    {
        return $this->hasMany(PromotionSector::class);
    }
}
