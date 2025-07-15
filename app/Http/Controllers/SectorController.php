<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectorController extends Controller
{
    public function create()
    {
        try {
            $sectors = Sector::all();
            return view('dashboard.Sectors.new', compact('sectors'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Erreur interne');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_sector' => 'required|string|max:255',
        ]);

        try {
            Sector::create([
                'name_sector' => $request->name_sector,
            ]);
            return redirect()->back()->with('success', 'Filière ajoutée avec succès.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Erreur lors de l\'enregistrement');
        }
    }

    public function edit(Sector $sector)
    {
        try {
            return view('dashboard.Sectors.edit', compact('sector'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Erreur interne');
        }
    }

    public function update(Request $request, Sector $sector)
    {
        $request->validate([
            'name_sector' => 'required|string|max:255',
        ]);

        try {
            $sector->update([
                'name_sector' => $request->name_sector,
            ]);
            return redirect()->route('sector.create')->with('success', 'Filière mise à jour.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Erreur lors de la mise à jour');
        }
    }

    public function destroy(Sector $sector)
    {
        try {
            $sector->delete();
            return redirect()->back()->with('success', 'Filière supprimée.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Erreur lors de la suppression');
        }
    }

    public function disablesector(Sector $year) {
        try {

            $year->update(['status' => false]);

            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la désactivation de la filère : ' . $e->getMessage());
            return redirect()->back()->with('error', __('messages.generic_error'));
        }
    }


    public function activatesector(Sector $year) {
        try {
            $year->update(['status' => true]);

            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'activation de la filière : ' . $e->getMessage());
            return redirect()->back()->with('error', __('messages.generic_error'));
        }
    }




}
