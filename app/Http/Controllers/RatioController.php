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
    public function create()
    {
        $years = Year::where('status', true)->get();
        return view('dashboard.ratios.create', compact('years'));
    }

    // ─── API : filières d'une année ───────────────────────────────────
    public function getSectorsByYear($yearId)
    {
        $sectors = SectorYear::with('sector')
            ->where('year_id', $yearId)
            ->get()
            ->map(fn($s) => [
                'id'   => $s->sector->id,
                'name' => $s->sector->name_sector,
            ]);

        return response()->json($sectors);
    }

    // ─── API : promotions d'une filière pour une année ────────────────
    public function getPromotionsByYearAndSector($yearId, $sectorId)
    {
        $sectorYear = SectorYear::where('year_id', $yearId)
            ->where('sector_id', $sectorId)
            ->first();

        if (!$sectorYear) return response()->json([]);

        $promotions = PromotionSector::where('sector_year_id', $sectorYear->id)
            ->get(['id', 'promotion_sector as name']);

        return response()->json($promotions);
    }

    // ─── API : matières (avec semestre) + classes + coefficients ──────
    // semester dans promotion_subjects : null = S1+S2, 1 = S1 seulement, 2 = S2 seulement
    public function getSubjectsAndClasses($promotionSectorId, $yearId)
    {
        $subjects = DB::table('promotion_subjects as ps')
            ->join('subjects as s', 's.id', '=', 'ps.subject_id')
            ->where('ps.promotion_sector_id', $promotionSectorId)
            ->select('s.id', 's.name', 'ps.semester')
            ->distinct()
            ->orderBy('s.name')
            ->get();

        $classes = PromotionClassroom::where('promotion_sector_id', $promotionSectorId)
            ->where('year_id', $yearId)
            ->get(['id', 'name']);

        // Ratios déjà enregistrés — on inclut semester pour pré-remplir l'interface
        $ratios = Ratio::where('promotion_sector_id', $promotionSectorId)
            ->where('year_id', $yearId)
            ->get(['id', 'subject_id', 'classroom_id', 'coefficient', 'semester']);

        return response()->json([
            'subjects' => $subjects, // [{id, name, semester}]
            'classes'  => $classes,
            'ratios'   => $ratios,
        ]);
    }

    // ─── Enregistrement des coefficients ─────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'year_id'                  => 'required|exists:years,id',
            'promotion_id'             => 'required|exists:promotion_sectors,id',
            'ratios'                   => 'required|array',
            'ratios.*.subject_id'      => 'required|exists:subjects,id',
            'ratios.*.classroom_ids'   => 'nullable|array',
            'ratios.*.classroom_ids.*' => 'exists:promotion_classrooms,id',
            'ratios.*.coefficient'     => 'required|integer|min:1',
            'ratios.*.semester'        => 'nullable|integer|in:1,2',
        ]);

        foreach ($request->ratios as $data) {
            $classroomIds = $data['classroom_ids'] ?? [];

            // null = S1+S2, 1 = S1 seulement, 2 = S2 seulement
            $semester = isset($data['semester']) && in_array((int)$data['semester'], [1, 2])
                ? (int)$data['semester']
                : null;

            // Supprimer les ratios des classes décochées pour cette matière
            Ratio::where('year_id',            $request->year_id)
                ->where('promotion_sector_id', $request->promotion_id)
                ->where('subject_id',          $data['subject_id'])
                ->whereNotIn('classroom_id',   $classroomIds)
                ->delete();

            foreach ($classroomIds as $classroomId) {
                Ratio::updateOrCreate(
                    [
                        'year_id'             => $request->year_id,
                        'promotion_sector_id' => $request->promotion_id,
                        'classroom_id'        => $classroomId,
                        'subject_id'          => $data['subject_id'],
                    ],
                    [
                        'coefficient' => $data['coefficient'],
                        'semester'    => $semester,
                    ]
                );
            }
        }

        return back()->with('success', 'Coefficients enregistrés avec succès.');
    }
}
