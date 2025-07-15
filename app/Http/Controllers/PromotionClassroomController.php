<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Year;
use App\Models\Sector;
use App\Models\SectorYear;
use App\Models\PromotionSector;
use App\Models\PromotionClassroom;

class PromotionClassroomController extends Controller
{
    public function create()
    {
        $years = Year::where('status', true)->get();
        return view('dashboard.classrooms.create', compact('years'));
    }

  public function store(Request $request)
{
    $request->validate([
        'year_id' => 'required|exists:years,id',
        'sector_id' => 'required|exists:sectors,id',
        'promotions' => 'required|array',
        'promotions.*' => 'required|exists:promotion_sectors,id',
        'counts' => 'required|array',
        'counts.*' => 'required|integer|min:1|max:26' // Limite à 26 classes (A-Z)
    ]);

    $promotionSectors = PromotionSector::whereIn('id', $request->promotions)
        ->get()
        ->keyBy('id');

    foreach ($request->promotions as $index => $promotionId) {
        $count = $request->counts[$index];
        $baseName = $promotionSectors[$promotionId]->promotion_sector;

        // Supprimer les suffixes existants (-A, -B, etc.) pour éviter les doublons
        $baseName = preg_replace('/-\w$/', '', $baseName);

        for ($i = 0; $i < $count; $i++) {
            // Ajouter le suffixe seulement si plus d'une classe
            $name = $count > 1 ? $baseName . '-' . chr(65 + $i) : $baseName;

            PromotionClassroom::updateOrCreate([
                'year_id' => $request->year_id,
                'sector_id' => $request->sector_id,
                'promotion_sector_id' => $promotionId,
                'name' => $name,
            ]);
        }
    }

    return redirect()->route('subject.create')
        ->with('success', 'Les classes ont été enregistrées avec succès.');
}


    public function getSectorsByYear($yearId)
    {
        $sectors = Sector::whereHas('sectorYears', function ($q) use ($yearId) {
            $q->where('year_id', $yearId);
        })->get(['id', 'name_sector as name']);

        return response()->json($sectors);
    }

    public function getPromotions($yearId, $sectorId)
    {
        $sectorYear = SectorYear::where('year_id', $yearId)
            ->where('sector_id', $sectorId)
            ->first();

        if (!$sectorYear) return response()->json([]);

        $promotions = PromotionSector::where('sector_year_id', $sectorYear->id)
            ->get(['id', 'promotion_sector as name']);

        return response()->json($promotions);
    }
}

