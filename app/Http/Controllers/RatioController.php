<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Year;
use App\Models\SectorYear;
use App\Models\PromotionSector;
use App\Models\Subject;
use App\Models\Ratio;
use App\Models\PromotionClassroom;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class RatioController extends Controller
{
    // Affichage du formulaire de création des coefficients
    public function create()
    {
        $years = Year::where('status', true)->get();
        return view('dashboard.ratios.create', compact('years'));
    }

    // API - Obtenir les filières d'une année
    public function getSectorsByYear($yearId)
    {
        $sectors = SectorYear::with('sector')
            ->where('year_id', $yearId)
            ->get()
            ->map(fn($s) => ['id' => $s->sector->id, 'name' => $s->sector->name_sector]);

        return response()->json($sectors);
    }

            // API - Obtenir les promotions d'une filière pour une année donnée
        public function getPromotionsByYearAndSector($yearId, $sectorId)
        {
            $sectorYear = SectorYear::where('year_id', $yearId)
                ->where('sector_id', $sectorId)
                ->first();

            if (!$sectorYear) {
                Log::info("No SectorYear found for year_id=$yearId and sector_id=$sectorId");
                return response()->json([]);
            }

            $promotions = PromotionSector::where('sector_year_id', $sectorYear->id)
                ->get(['id', 'promotion_sector as name']);

            Log::info("Promotions found:", $promotions->toArray());

            return response()->json($promotions);
        }


    // API - Obtenir les matières et classes d'une promotion pour une année donnée
   public function getSubjectsAndClasses($promotionSectorId, $yearId)
    {
        // Matières liées à la promotion
        $subjects = DB::table('promotion_subjects as ps')
            ->join('subjects as s', 's.id', '=', 'ps.subject_id')
            ->where('ps.promotion_sector_id', $promotionSectorId)
            ->select('s.id', 's.name')
            ->distinct()
            ->get();

        // Classes de la promotion pour cette année
        $classes = PromotionClassroom::where('promotion_sector_id', $promotionSectorId)
            ->where('year_id', $yearId)
            ->get(['id', 'name']);

        // Coefficients déjà enregistrés
        $ratios = Ratio::where('promotion_sector_id', $promotionSectorId)
            ->where('year_id', $yearId)
            ->get();

        return response()->json([
            'subjects' => $subjects,
            'classes' => $classes,
            'ratios' => $ratios
        ]);
    }


    // Enregistrement des coefficients
    public function store(Request $request)
    {
        $request->validate([
            'year_id' => 'required|exists:years,id',
            'promotion_id' => 'required|exists:promotion_sectors,id',
            'ratios' => 'required|array',
            'ratios.*.subject_id' => 'required|exists:subjects,id',
            'ratios.*.classroom_ids' => 'required|array',
            'ratios.*.coefficient' => 'required|integer|min:1',
        ]);

        foreach ($request->ratios as $data) {
            foreach ($data['classroom_ids'] as $classroomId) {
                Ratio::updateOrCreate([
                    'year_id' => $request->year_id,
                    'promotion_sector_id' => $request->promotion_id,
                    'classroom_id' => $classroomId,
                    'subject_id' => $data['subject_id']
                ], [
                    'coefficient' => $data['coefficient']
                ]);
            }
        }

        return back()->with('success', 'Coefficients enregistrés avec succès.');
    }
}
