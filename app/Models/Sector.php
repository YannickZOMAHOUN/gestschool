<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
     protected $fillable=[
        'name_sector',
        'status',
    ];
        public function sectorYears()
    {
        return $this->hasMany(SectorYear::class);
    }

}
