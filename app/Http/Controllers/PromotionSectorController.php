<?php

namespace App\Http\Controllers;

use App\Models\Year;
use App\Models\Sector;
use App\Models\SectorYear;
use App\Models\PromotionSector;
use App\Models\PromotionClassroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PromotionSectorController extends Controller
{
    public function create()
    {
        $years = Year::where('status', true)->get();
        return view('dashboard.promotions.create', compact('years'));
    }

    public function getSectorsByYear($year)
    {
        try {
            $sectors = SectorYear::where('year_id', $year)
                ->with('sector')
                ->get()
                ->map(function ($sy) {
                    return [
                        'id'          => $sy->id,
                        'name_sector' => $sy->sector->name_sector,
                    ];
                });

            // Promotions déjà enregistrées avec leur statut de verrouillage
            $registeredPromotions = PromotionSector::whereHas('sectorYear', fn($q) => $q->where('year_id', $year))
                ->get()
                ->map(function ($ps) {
                    // Verrouillée si elle a au moins une classe OU des notes
                    $hasClassrooms = PromotionClassroom::where('promotion_sector_id', $ps->id)->exists();
                    // Chaîne : notes → recordings → promotion_classrooms
                    $clIds2 = \App\Models\PromotionClassroom::where('promotion_sector_id', $ps->id)->pluck('id');
                    $hasNotes = \App\Models\Note::whereHas('recording', fn($q) =>
                        $q->whereIn('classroom_id', $clIds2)
                    )->exists();

                    return [
                        'label'     => $ps->promotion_sector,
                        'locked'    => $hasClassrooms || $hasNotes,
                        'reason'    => $hasNotes      ? 'notes'
                                     : ($hasClassrooms ? 'classes' : null),
                    ];
                });

            return response()->json([
                'sectors'              => $sectors,
                'registeredPromotions' => $registeredPromotions,
            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Erreur serveur.'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'sector_year_ids' => 'required|array',
        ]);

        try {
            // ── Passe 1 : vérifier qu'aucune promotion décochée n'est verrouillée ──
            foreach ($request->sector_year_ids as $sectorYearId => $submittedLabels) {

                // Promotions actuellement en base pour ce sector_year
                $existing = PromotionSector::where('sector_year_id', $sectorYearId)->get();

                foreach ($existing as $ps) {
                    // Cette promotion était cochée avant mais ne l'est plus maintenant
                    if (!in_array($ps->promotion_sector, $submittedLabels)) {

                        $hasClassrooms = PromotionClassroom::where('promotion_sector_id', $ps->id)->exists();
                        // Chaîne : notes → recordings → promotion_classrooms
                        $clIds3 = \App\Models\PromotionClassroom::where('promotion_sector_id', $ps->id)->pluck('id');
                        $hasNotes = \App\Models\Note::whereHas('recording', fn($q) =>
                            $q->whereIn('classroom_id', $clIds3)
                        )->exists();

                        if ($hasNotes) {
                            return back()->withInput()->withErrors([
                                'error' => "Impossible de supprimer la promotion « {$ps->promotion_sector} » : "
                                         . "elle contient déjà des notes enregistrées."
                            ]);
                        }

                        if ($hasClassrooms) {
                            return back()->withInput()->withErrors([
                                'error' => "Impossible de supprimer la promotion « {$ps->promotion_sector} » : "
                                         . "des classes lui sont déjà associées. Supprimez d'abord les classes."
                            ]);
                        }
                    }
                }
            }

            // ── Passe 2 : tout est OK → appliquer les changements ──────────────
            foreach ($request->sector_year_ids as $sectorYearId => $submittedLabels) {

                $existing = PromotionSector::where('sector_year_id', $sectorYearId)->get();
                $existingLabels = $existing->pluck('promotion_sector')->toArray();

                // Supprimer uniquement celles qui ont été décochées (et non verrouillées — déjà validé)
                foreach ($existing as $ps) {
                    if (!in_array($ps->promotion_sector, $submittedLabels)) {
                        $ps->delete();
                    }
                }

                // Créer uniquement les nouvelles (celles qui n'existent pas encore)
                foreach ($submittedLabels as $label) {
                    if (!in_array($label, $existingLabels)) {
                        PromotionSector::create([
                            'sector_year_id'   => $sectorYearId,
                            'promotion_sector' => $label,
                        ]);
                    }
                }
            }

            Log::info("Promotions mises à jour", [
                'sector_year_ids' => array_keys($request->sector_year_ids),
            ]);

            return redirect()->route('promotion-classrooms.create')
                ->with('success', 'Promotions enregistrées avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'enregistrement des promotions : " . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur lors de l\'enregistrement.');
        }
    }
}
