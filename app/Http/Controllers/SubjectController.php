<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Year;
use App\Models\SectorYear;
use App\Models\Subject;
use App\Models\PromotionSector;
use App\Models\PromotionSubject;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
    public function create()
    {
        $years = Year::where('status', true)->get();
        $allSubjects = Subject::orderBy('name')->get();
        return view('dashboard.subjects.create', compact('years', 'allSubjects'));
    }

    public function getSectorsByYear($yearId)
    {
        $sectors = SectorYear::with('sector')
            ->where('year_id', $yearId)
            ->get()
            ->map(function ($sy) {
                return [
                    'id' => $sy->sector->id,
                    'name' => $sy->sector->name_sector
                ];
            });
        return response()->json($sectors);
    }

    public function getPromotionsByYearSector($yearId, $sectorId)
    {
        $sectorYear = SectorYear::where('year_id', $yearId)
            ->where('sector_id', $sectorId)
            ->first();

        if (!$sectorYear) return response()->json([]);

        $promotions = PromotionSector::where('sector_year_id', $sectorYear->id)
            ->get();

        $result = $promotions->map(function ($promotion) {
            $subjects = PromotionSubject::where('promotion_sector_id', $promotion->id)
                ->with('subject')
                ->get()
                ->pluck('subject.name')
                ->toArray();

            return [
                'id' => $promotion->id,
                'promotion_sector' => $promotion->promotion_sector,
                'subjects' => $subjects
            ];
        });

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $request->validate([
            'year_id' => 'required|exists:years,id',
            'sector_id' => 'required|exists:sectors,id',
            'promotion_ids' => 'required|array',
            'subjects_by_promotion' => 'required|array',
        ]);

        $sectorYear = SectorYear::where('year_id', $request->year_id)
            ->where('sector_id', $request->sector_id)
            ->firstOrFail();

        foreach ($request->promotion_ids as $index => $promotionId) {
            $promotion = PromotionSector::where('id', $promotionId)
                ->where('sector_year_id', $sectorYear->id)
                ->firstOrFail();

            $subjectNames = array_filter(
                array_map('trim', explode(';', $request->subjects_by_promotion[$index])),
                fn($name) => !empty($name)
            );

            PromotionSubject::where('promotion_sector_id', $promotionId)->delete();

            foreach ($subjectNames as $name) {
                $subject = Subject::firstOrCreate(['name' => $name]);
                PromotionSubject::create([
                    'promotion_sector_id' => $promotionId,
                    'subject_id' => $subject->id,
                ]);
            }

            Log::info("Matières enregistrées pour la promotion {$promotionId}: " . implode(', ', $subjectNames));
        }

        return redirect()->route('ratio.create')->with('success', 'Matières enregistrées avec succès.');
    }
}
