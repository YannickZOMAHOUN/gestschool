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
        $years       = Year::where('status', true)->get();
        $allSubjects = Subject::orderBy('name')->get();
        return view('dashboard.subjects.create', compact('years', 'allSubjects'));
    }

    // ─── API : filières d'une année ───────────────────────────────────
    public function getSectorsByYear($yearId)
    {
        $sectors = SectorYear::with('sector')
            ->where('year_id', $yearId)
            ->get()
            ->map(fn($sy) => [
                'id'   => $sy->sector->id,
                'name' => $sy->sector->name_sector,
            ]);

        return response()->json($sectors);
    }

    // ─── API : promotions avec leurs matières + semester ─────────────
    // semester : null = S1+S2, 1 = S1 seulement, 2 = S2 seulement
    public function getPromotionsByYearSector($yearId, $sectorId)
    {
        $sectorYear = SectorYear::where('year_id', $yearId)
            ->where('sector_id', $sectorId)
            ->first();

        if (!$sectorYear) return response()->json([]);

        $promotions = PromotionSector::where('sector_year_id', $sectorYear->id)->get();

        $result = $promotions->map(function ($promotion) {
            $subjects = PromotionSubject::where('promotion_sector_id', $promotion->id)
                ->with('subject')
                ->get()
                ->map(fn($ps) => [
                    'name'     => $ps->subject->name,
                    'semester' => $ps->semester, // null | 1 | 2
                ])
                ->values()
                ->toArray();

            return [
                'id'               => $promotion->id,
                'promotion_sector' => $promotion->promotion_sector,
                'subjects'         => $subjects,
            ];
        });

        return response()->json($result);
    }

    // ─── Enregistrement des matières par promotion ────────────────────
    // subjects_by_promotion[] = JSON : [{"name":"...","semester":null|1|2}, ...]
    public function store(Request $request)
    {
        $request->validate([
            'year_id'               => 'required|exists:years,id',
            'sector_id'             => 'required|exists:sectors,id',
            'promotion_ids'         => 'required|array',
            'subjects_by_promotion' => 'required|array',
        ]);

        $sectorYear = SectorYear::where('year_id', $request->year_id)
            ->where('sector_id', $request->sector_id)
            ->firstOrFail();

        foreach ($request->promotion_ids as $index => $promotionId) {
            $promotion = PromotionSector::where('id', $promotionId)
                ->where('sector_year_id', $sectorYear->id)
                ->firstOrFail();

            $subjectEntries = json_decode($request->subjects_by_promotion[$index] ?? '[]', true) ?? [];

            PromotionSubject::where('promotion_sector_id', $promotionId)->delete();

            foreach ($subjectEntries as $entry) {
                $name = trim($entry['name'] ?? '');
                if (empty($name)) continue;

                $semRaw   = $entry['semester'] ?? null;
                $semester = ($semRaw === 1 || $semRaw === '1') ? 1
                    : (($semRaw === 2 || $semRaw === '2') ? 2 : null);

                $subject = Subject::firstOrCreate(['name' => $name]);

                PromotionSubject::create([
                    'promotion_sector_id' => $promotionId,
                    'subject_id'          => $subject->id,
                    'semester'            => $semester,
                ]);
            }

            Log::info("Matières enregistrées pour la promotion {$promotionId}", [
                'count' => count($subjectEntries),
            ]);
        }

        return redirect()->route('ratio.create')
            ->with('success', 'Matières enregistrées avec succès.');
    }
}
