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

    /**
     * Retourne les classes déjà enregistrées pour une année + filière,
     * avec un flag "has_notes" pour bloquer leur suppression côté vue.
     */
    public function getExistingClassrooms($yearId, $sectorId)
    {
        $classrooms = PromotionClassroom::where('year_id', $yearId)
            ->where('sector_id', $sectorId)
            ->orderBy('name')
            ->get(['id', 'promotion_sector_id', 'name'])
            ->map(function ($classroom) {
                // Chaîne : notes → recordings → promotion_classrooms
                $classroom->has_notes = \App\Models\Note::whereHas('recording', fn($q) =>
                    $q->where('classroom_id', $classroom->id)
                )->exists();
                return $classroom;
            });

        return response()->json($classrooms);
    }

    public function store(Request $request)
    {
        $request->validate([
            'year_id'      => 'required|exists:years,id',
            'sector_id'    => 'required|exists:sectors,id',
            'promotions'   => 'required|array',
            'promotions.*' => 'required|exists:promotion_sectors,id',
            'counts'       => 'required|array',
            'counts.*'     => 'required|integer|min:0|max:26',
        ]);

        $promotionSectors = PromotionSector::whereIn('id', $request->promotions)
            ->get()
            ->keyBy('id');

        // ── Passe 1 : validation globale AVANT toute modification ──────────
        // On vérifie d'abord que aucune réduction ne tente de supprimer
        // une classe ayant déjà reçu des notes. Si une erreur est détectée,
        // on retourne immédiatement sans avoir touché à la base.
        foreach ($request->promotions as $index => $promotionId) {
            $count = (int) $request->counts[$index];

            $existingClassrooms = PromotionClassroom::where('year_id', $request->year_id)
                ->where('sector_id', $request->sector_id)
                ->where('promotion_sector_id', $promotionId)
                ->orderBy('name')
                ->get();

            $existingCount = $existingClassrooms->count();

            // Uniquement si on réduit le nombre de classes
            if ($count < $existingCount) {
                $toDelete = $existingClassrooms->slice($count);

                foreach ($toDelete as $classroom) {
                    // Chaîne : notes → recordings → promotion_classrooms
                    $hasNotes = \App\Models\Note::whereHas('recording', fn($q) =>
                        $q->where('classroom_id', $classroom->id)
                    )->exists();

                    if ($hasNotes) {
                        return back()
                            ->withInput()
                            ->withErrors([
                                'error' => "Impossible de supprimer la classe « {$classroom->name} » : "
                                         . "elle contient déjà des notes enregistrées. "
                                         . "Réduire en dessous de {$existingCount} n'est pas autorisé pour cette promotion."
                            ]);
                    }
                }
            }
        }

        // ── Passe 2 : toutes les validations sont OK → on applique ─────────
        foreach ($request->promotions as $index => $promotionId) {
            $count    = (int) $request->counts[$index];
            $baseName = $promotionSectors[$promotionId]->promotion_sector;

            // Nettoyer les éventuels suffixes -A, -B déjà présents dans le nom de base
            $baseName = preg_replace('/-[A-Z]$/', '', $baseName);

            $existingClassrooms = PromotionClassroom::where('year_id', $request->year_id)
                ->where('sector_id', $request->sector_id)
                ->where('promotion_sector_id', $promotionId)
                ->orderBy('name')
                ->get();

            $existingCount = $existingClassrooms->count();

            // ── Cas 1 : réduction → supprimer les classes en trop ──────────
            if ($count < $existingCount) {
                $toDelete = $existingClassrooms->slice($count);
                PromotionClassroom::whereIn('id', $toDelete->pluck('id'))->delete();
            }

            // ── Cas 2 : augmentation → créer uniquement les nouvelles ──────
            if ($count > $existingCount) {
                for ($i = $existingCount; $i < $count; $i++) {
                    $name = $count > 1
                        ? $baseName . '-' . chr(65 + $i)
                        : $baseName;

                    PromotionClassroom::create([
                        'year_id'             => $request->year_id,
                        'sector_id'           => $request->sector_id,
                        'promotion_sector_id' => $promotionId,
                        'name'                => $name,
                    ]);
                }
            }

            // ── Cas 3 : count === existingCount → rien à faire ─────────────
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
