<?php
namespace App\Http\Controllers;

use App\Models\Year;
use App\Models\Sector;
use App\Models\SectorYear;
use App\Models\PromotionSector;
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
            Log::info("Année sélectionnée : {$year}");
            $sectors = SectorYear::where('year_id', $year)
                ->with('sector') // Relation vers `sectors`
                ->get()
                ->map(function ($sy) {
                    return [
                        'id' => $sy->id,
                        'name_sector' => $sy->sector->name_sector,
                    ];
                });

            $registered = PromotionSector::whereHas('sectorYear', fn($q) => $q->where('year_id', $year))
                ->get()
                ->pluck('promotion_sector')
                ->toArray();

            return response()->json([
                'sectors' => $sectors,
                'registeredPromotions' => $registered
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
        // Supprimer les anciennes promotions enregistrées pour les sectors_years sélectionnés
        PromotionSector::whereIn('sector_year_id', array_keys($request->sector_year_ids))->delete();

        $data = [];

        foreach ($request->sector_year_ids as $sectorYearId => $promotions) {
            foreach ($promotions as $promotionLabel) {
                $data[] = [
                    'sector_year_id' => $sectorYearId,
                    'promotion_sector' => $promotionLabel,
                ];
            }
        }

        PromotionSector::insert($data);

        Log::info("Promotions enregistrées pour sectors_year_ids", [
            'ids' => array_keys($request->sector_year_ids),
            'total' => count($data),
        ]);

        return redirect()->route('promotion-classrooms.create')->with('success', 'Promotions enregistrées avec succès.');
    } catch (\Exception $e) {
        Log::error("Erreur lors de l'enregistrement des promotions : " . $e->getMessage());
        return back()->with('error', 'Erreur lors de l’enregistrement.');
    }
}




}
