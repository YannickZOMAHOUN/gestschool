<?php

namespace App\Http\Controllers;

use App\Models\Year;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class YearController extends Controller
{
    public function create()
    {
        try {
            $years = Year::orderByDesc('id')->get();
            return view('dashboard.years.create', compact('years'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            abort(404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|string|unique:years,year|max:20',
        ]);

        try {
            Year::query()->create([
                'year'   => $request->year,
                'status' => false, // toujours inactive à la création
            ]);
            return redirect()->route('year.create')
                ->with('success', 'Année scolaire créée avec succès.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la création.');
        }
    }

    public function edit(Year $year)
    {
        try {
            return view('dashboard.years.edit', compact('year'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            abort(404);
        }
    }

    public function update(Request $request, Year $year)
    {
        $request->validate([
            'year' => 'required|string|unique:years,year,' . $year->id . '|max:20',
        ]);

        try {
            $year->update(['year' => $request->year]);
            return redirect()->route('year.create')
                ->with('success', 'Année scolaire modifiée avec succès.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la modification.');
        }
    }

    public function destroy(Year $year)
    {
        try {
            // Empêcher la suppression de l'année active
            if ($year->status) {
                return redirect()->back()
                    ->with('error', 'Impossible de supprimer l\'année scolaire active.');
            }
            $year->delete();
            return redirect()->back()
                ->with('success', 'Année scolaire supprimée.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    /**
     * Active l'année sélectionnée et désactive TOUTES les autres.
     * Contrainte : une seule année active à la fois.
     */
    public function activateyear(Year $year)
    {
        try {
            DB::transaction(function () use ($year) {
                // Désactiver toutes les autres années
                Year::where('id', '!=', $year->id)->update(['status' => false]);
                // Activer celle-ci
                $year->update(['status' => true]);
            });

            return redirect()->back()
                ->with('success', 'Année « ' . $year->year . ' » activée. Les autres ont été désactivées.');
        } catch (\Exception $e) {
            Log::error('Activation année : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l\'activation.');
        }
    }

    /**
     * Désactive l'année sans en activer une autre.
     */
    public function disableyear(Year $year)
    {
        try {
            $year->update(['status' => false]);
            return redirect()->back()
                ->with('success', 'Année « ' . $year->year . ' » désactivée.');
        } catch (\Exception $e) {
            Log::error('Désactivation année : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la désactivation.');
        }
    }
}
