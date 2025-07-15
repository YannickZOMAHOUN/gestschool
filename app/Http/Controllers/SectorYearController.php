<?php

namespace App\Http\Controllers;
use App\Models\Year;
use App\Models\Sector;
use App\Models\SectorYear;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SectorYearController extends Controller
{
    public function create()
    {
        try {
            $years = Year::where('status', true)->get();
            $sectors = Sector::all();

            // Si aucune année active, on évite une erreur
            $sectoryears = [];
            if (!$years->isEmpty()) {
                $sectoryears = SectorYear::where('year_id', $years->first()->id)
                    ->pluck('sector_id')
                    ->toArray();
            }

            return view('dashboard.sectors.create', compact('years', 'sectors', 'sectoryears'));
        } catch (\Exception $e) {
            Log::error('Erreur dans SectorController@create: ' . $e->getMessage());
            abort(500, 'Erreur interne du serveur');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|exists:years,id',
            'sectors' => 'array', // peut être vide
            'sectors.*' => 'exists:sectors,id',
        ]);

        try {
            DB::beginTransaction();

            // Supprimer les anciens enregistrements pour cette année
            SectorYear::where('year_id', $request->year)->delete();

            // Réinsérer les nouvelles associations
            if ($request->has('sectors')) {
                foreach ($request->sectors as $sectorId) {
                    SectorYear::create([
                        'year_id' => $request->year,
                        'sector_id' => $sectorId,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('promotionbysector.create')->with('success', 'Filières enregistrées avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

        public function getSectorsByYear($yearId)
        {
            $sectors = Sector::all();
            $selected = SectorYear::where('year_id', $yearId)->pluck('sector_id')->toArray();

            return response()->json([
                'sectors' => $sectors,
                'selected' => $selected,
            ]);
        }
}
