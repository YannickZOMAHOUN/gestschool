<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sector;


class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sector::create(['name_sector' => 'IMI']);
        Sector::create(['name_sector' => 'BTP']);
        Sector::create(['name_sector' => 'DPB']);
        Sector::create(['name_sector' => 'F3']);
        Sector::create(['name_sector' => 'F2']);
        Sector::create(['name_sector' => 'F1']);
        Sector::create(['name_sector' => 'G1']);
        Sector::create(['name_sector' => 'G2']);
        Sector::create(['name_sector' => 'FM']);
        Sector::create(['name_sector' => 'MA']);
        Sector::create(['name_sector' => 'EA']);
    }
}
