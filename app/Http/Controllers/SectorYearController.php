<?php

namespace App\Http\Controllers;

use App\Models\Year;
use App\Models\Sector;
use App\Models\SectorYear;
use App\Models\PromotionSector;
use App\Models\PromotionClassroom;
use App\Models\Note;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SectorYearController extends Controller
{
    public function create()
    {
        try {
            $years   = Year::where('status', true)->get();
            $sectors = Sector::all();

            $sectoryears = [];
            if (!$years->isEmpty()) {
                $sectoryears = SectorYear::where('year_id', $years->first()->id)
                    ->pluck('sector_id')
                    ->toArray();
            }

            return view('dashboard.sectors.create', compact('years', 'sectors', 'sectoryears'));
        } catch (\Exception $e) {
            Log::error('[SectorYear@create] ' . $e->getMessage());
            abort(500, 'Erreur interne du serveur');
        }
    }

    public function store(Request $request)
    {
        Log::debug('[SectorYear@store] ════════ DÉBUT ════════');
        Log::debug('[SectorYear@store] Données brutes reçues : ' . json_encode($request->all()));

        $request->validate([
            'year'      => 'required|exists:years,id',
            'sectors'   => 'array',
            'sectors.*' => 'exists:sectors,id',
        ]);

        $yearId = (int) $request->year;

        // Cast strict de tous les IDs soumis en entiers
        $submittedIds = collect($request->input('sectors', []))
            ->map(fn($id) => (int) $id)
            ->toArray();

        Log::debug('[SectorYear@store] year_id           : ' . $yearId);
        Log::debug('[SectorYear@store] sectors[] bruts   : ' . json_encode($request->input('sectors', [])));
        Log::debug('[SectorYear@store] submittedIds (int): ' . json_encode($submittedIds));

        // Toutes les filières actuellement liées à cette année
        $existingSectorYears = SectorYear::where('year_id', $yearId)->get();

        // IDs existants en base (entiers)
        $existingIds = $existingSectorYears
            ->map(fn($sy) => (int) $sy->sector_id)
            ->toArray();

        Log::debug('[SectorYear@store] existingIds (base): ' . json_encode($existingIds));

        // IDs verrouillés = filières existantes qui ont des promotions.
        // Ces filières ne peuvent JAMAIS être supprimées, qu'elles soient
        // soumises ou non (les inputs disabled ne sont pas envoyés par le navigateur).
        $lockedSectorIds = SectorYear::where('year_id', $yearId)
            ->get()
            ->filter(fn($sy) => PromotionSector::where('sector_year_id', $sy->id)->exists())
            ->map(fn($sy) => (int) $sy->sector_id)
            ->toArray();

        Log::debug('[SectorYear@store] lockedSectorIds   : ' . json_encode($lockedSectorIds));

        // Filières à supprimer = existantes, absentes des soumises ET non verrouillées
        $toRemove = $existingSectorYears->filter(
            fn($sy) => !in_array((int) $sy->sector_id, $submittedIds, true)
                    && !in_array((int) $sy->sector_id, $lockedSectorIds, true)
        );

        // Filières à créer = soumises mais absentes des existantes
        $toCreate = collect($submittedIds)->filter(
            fn($id) => !in_array($id, $existingIds, true)
        );

        Log::debug('[SectorYear@store] toRemove sector_ids : ' . json_encode($toRemove->pluck('sector_id')->toArray()));
        Log::debug('[SectorYear@store] toCreate sector_ids : ' . json_encode($toCreate->values()->toArray()));

        // ── Passe 1 : vérifier que les filières à supprimer ne sont pas verrouillées ──
        foreach ($toRemove as $sy) {
            $sectorName = optional($sy->sector)->name_sector ?? "ID {$sy->sector_id}";

            Log::debug("[SectorYear@store] Vérif suppression → sector_id={(int)$sy->sector_id} nom={$sectorName}");

            // Des promotions ?
            $psIds = PromotionSector::where('sector_year_id', $sy->id)->pluck('id');

            Log::debug("[SectorYear@store]   psIds (promotions) : " . json_encode($psIds->toArray()));

            if ($psIds->isEmpty()) {
                Log::debug("[SectorYear@store]   → Aucune promotion, suppression libre.");
                continue;
            }

            // Des classes ?
            $clIds = PromotionClassroom::whereIn('promotion_sector_id', $psIds)->pluck('id');

            Log::debug("[SectorYear@store]   clIds (classes) : " . json_encode($clIds->toArray()));

            if ($clIds->isNotEmpty()) {
                // Des notes dans ces classes ?
                $hasNotes = Note::whereHas(
                    'recording',
                    fn($q) => $q->whereIn('classroom_id', $clIds)
                )->exists();

                Log::debug("[SectorYear@store]   hasNotes : " . ($hasNotes ? 'OUI' : 'NON'));

                if ($hasNotes) {
                    Log::warning("[SectorYear@store] BLOQUÉ (notes) → {$sectorName}");
                    return back()->withInput()->withErrors([
                        'error' => "Impossible de retirer la filière « {$sectorName} » : "
                                 . "elle contient des notes enregistrées.",
                    ]);
                }

                Log::warning("[SectorYear@store] BLOQUÉ (classes) → {$sectorName}");
                return back()->withInput()->withErrors([
                    'error' => "Impossible de retirer la filière « {$sectorName} » : "
                             . "des classes lui sont déjà associées. Supprimez d'abord les classes.",
                ]);
            }

            // Des promotions sans classes
            Log::warning("[SectorYear@store] BLOQUÉ (promotions) → {$sectorName}");
            return back()->withInput()->withErrors([
                'error' => "Impossible de retirer la filière « {$sectorName} » : "
                         . "des promotions lui sont déjà associées. Supprimez d'abord les promotions.",
            ]);
        }

        Log::debug('[SectorYear@store] Passe 1 OK — aucun verrou détecté.');

        // ── Passe 2 : appliquer le diff ──────────────────────────────────────────
        try {
            DB::beginTransaction();

            foreach ($toRemove as $sy) {
                Log::debug("[SectorYear@store] Suppression SectorYear id={$sy->id} sector_id={$sy->sector_id}");
                $sy->delete();
            }

            foreach ($toCreate as $sectorId) {
                Log::debug("[SectorYear@store] Création SectorYear year_id={$yearId} sector_id={$sectorId}");
                SectorYear::create([
                    'year_id'   => $yearId,
                    'sector_id' => $sectorId,
                ]);
            }

            DB::commit();

            Log::debug('[SectorYear@store] ════════ SUCCÈS ════════');

            return redirect()->route('promotionbysector.create')
                ->with('success', 'Filières enregistrées avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[SectorYear@store] EXCEPTION : ' . $e->getMessage());
            Log::error('[SectorYear@store] Trace : ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function getSectorsByYear($yearId)
    {
        Log::debug("[SectorYear@getSectorsByYear] year_id={$yearId}");

        $sectors  = Sector::all();
        $selected = SectorYear::where('year_id', $yearId)->pluck('sector_id')->toArray();

        // Filières verrouillées : celles qui ont des promotions
        $lockedIds = SectorYear::where('year_id', $yearId)
            ->get()
            ->filter(fn($sy) => PromotionSector::where('sector_year_id', $sy->id)->exists())
            ->pluck('sector_id')
            ->toArray();

        Log::debug("[SectorYear@getSectorsByYear] selected : " . json_encode($selected));
        Log::debug("[SectorYear@getSectorsByYear] locked   : " . json_encode($lockedIds));

        return response()->json([
            'sectors'  => $sectors,
            'selected' => $selected,
            'locked'   => $lockedIds,
        ]);
    }
}
