<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromotionSector extends Model
{
    use HasFactory;

    protected $fillable = ['sector_year_id', 'promotion_sector', 'promotion_id'];

    public function sectorYear()
    {
        return $this->belongsTo(SectorYear::class);
    }

    public function year()
    {
        return $this->sectorYear->year();
    }

   public function sector()
    {
        return $this->belongsTo(Sector::class);
    }


}

