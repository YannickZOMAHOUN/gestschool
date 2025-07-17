<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Year;
use App\Models\SectorYear;
use App\Models\PromotionSector;
use App\Models\PromotionClassroom;
use App\Models\Student;
use App\Models\Recording;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Affiche la liste des élèves avec années actives
     */
    public function index()
    {
        $years = Year::where('status', true)->get();
        return view('dashboard.students.list', compact('years'));
    }

    /**
     * Affiche la page de création d'un élève
     */
    public function create()
    {
        $years = Year::where('status', true)->get();
        return view('dashboard.students.create', compact('years'));
    }

    /**
     * Affiche le formulaire d'édition d'un élève
     */
     public function edit($id)
{
    $student = Student::with(['recordings.classroom'])->findOrFail($id);

    // Récupère le dernier enregistrement ou le premier
    $latestRecording = $student->recordings->sortByDesc('year_id')->first();

    $years = Year::where('status', true)->get();

    // Initialiser les collections
    $sectors = collect();
    $promotions = collect();
    $classrooms = collect();

    if ($latestRecording) {
        $year_id = $latestRecording->year_id;
        $classroom = $latestRecording->classroom;

        // Charger secteurs pour l'année
        $sectors = SectorYear::with('sector')
                    ->where('year_id', $year_id)
                    ->get()
                    ->pluck('sector');

        if ($classroom) {
            // Charger promotions pour le secteur et année
            $promotions = PromotionSector::where('sector_year_id', $classroom->promotion_sector_id)
                            ->get();

            // Charger classes pour la promotion
            $classrooms = PromotionClassroom::where('promotion_sector_id', $classroom->promotion_sector_id)
                            ->get();
        }
    }

    return view('dashboard.students.edit', compact(
        'student',
        'years',
        'sectors',
        'promotions',
        'classrooms',
        'latestRecording'
    ));
}

public function update(Request $request, $id)
{
    $request->validate([
        'matricule' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'surname' => 'required|string|max:255',
        'sex' => 'required|in:M,F',
        'birthday' => 'required|date',
        'birthplace' => 'nullable|string|max:255',
        'year_id' => 'required|exists:years,id',
        'sector_id' => 'required|exists:sectors,id',
        'promotion_id' => 'required|exists:promotion_sectors,id',
        'classroom_id' => 'required|exists:promotion_classrooms,id',
    ]);

    DB::beginTransaction();

    try {
        $student = Student::findOrFail($id);
        $student->update($request->only([
            'matricule', 'name', 'surname', 'sex', 'birthday', 'birthplace'
        ]));

        // Vérifie si un enregistrement existe déjà pour cette année
        $recording = $student->recordings()
                        ->where('year_id', $request->year_id)
                        ->first();

        if ($recording) {
            // Mise à jour de l'enregistrement existant
            $recording->update([
                'classroom_id' => $request->classroom_id,
            ]);
        } else {
            // Création d'un nouvel enregistrement
            Recording::create([
                'student_id' => $student->id,
                'year_id' => $request->year_id,
                'classroom_id' => $request->classroom_id,
            ]);
        }

        DB::commit();
        return redirect()->route('student.index')->with('success', 'Élève modifié avec succès.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Erreur lors de la mise à jour de l'élève: " . $e->getMessage());
        return back()->with('error', 'Une erreur est survenue lors de la mise à jour.');
    }
}
    /**
     * Ajout manuel d’un élève
     */
  public function store(Request $request)
{
    $rules = [
        'year_id' => 'required|exists:years,id',
        'name' => 'required|string|max:255',
        'surname' => 'required|string|max:255',
        'sex' => 'required|in:M,F',
        'birthday' => 'required|date|before_or_equal:today',
        'birthplace' => 'required|string|max:255',
        'number' => 'nullable|regex:/^(\d{2}\s?){3}\d{2}$/',
        'aptitude' => 'required|in:Apte,Inapte',
    ];

    $customMessages = [
        'birthday.before_or_equal' => 'La date de naissance doit être antérieure ou égale à aujourd\'hui.',
        'number.regex' => 'Le format du numéro de téléphone est invalide. Format attendu: 00 00 00 00',
        'matricule.unique' => 'Ce matricule est déjà utilisé par un autre élève.',
    ];

    $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();


    try {
        // Nettoyer et formater le numéro
        $cleanNumber = preg_replace('/\D/', '', $request->input('number'));
        $formattedNumber = $cleanNumber ? '+229 01 ' . implode(' ', str_split($cleanNumber, 2)) : null;

        $student = Student::create([
            'matricule' => $request->matricule,
            'name' => $request->name,
            'surname' => $request->surname,
            'sex' => $request->sex,
            'birthday' => $request->birthday,
            'birthplace' => $request->birthplace,
            'number' => $formattedNumber,
            'aptitude' => $request->aptitude,
        ]);

            $parent = User::create([
            'name' => $student->surname . ' ' . $student->name,
            'email' => 'parent_' . $student->matricule . '@lyteb.com',
            'password' => Hash::make('parent' . $student->matricule),
            'must_change_password' => false,
        ]);
        $parent->assignRole('Parent');

        // Liaison élève-parent
        $student->update(['user_id' => $parent->id]);
        // Vérifier que l'élève n'est pas déjà enregistré dans cette classe
        $existingRecording = Recording::where([
            'student_id' => $student->id,
            'year_id' => $request->year_id,
            'classroom_id' => $request->classroom_id,
        ])->exists();

        if ($existingRecording) {
            throw new \Exception('Cet élève est déjà enregistré dans cette classe pour cette année scolaire.');
        }

        Recording::create([
            'student_id' => $student->id,
            'year_id' => $request->year_id,
            'classroom_id' => $request->classroom_id,
        ]);

        DB::commit();

         return back()->with('success', 'Élève et compte parent créés.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Erreur ajout élève + parent : " . $e->getMessage());
        return back()->withErrors('Erreur lors de l\'ajout de l\'élève.');
    }
}



    public function getSectorsByYear($yearId)
    {
        $sectorYears = SectorYear::with('sector')
            ->where('year_id', $yearId)
            ->get();

        $sectors = $sectorYears->map(fn($sy) => [
            'id' => $sy->sector->id,
            'name' => $sy->sector->name_sector,
        ]);

        return response()->json($sectors);
    }

    /**
     * API : Promotions pour une année + filière
     */
    public function getPromotionsByYearSector($yearId, $sectorId)
    {
        $sectorYear = SectorYear::where('year_id', $yearId)
            ->where('sector_id', $sectorId)
            ->first();

        if (!$sectorYear) return response()->json([]);

        $promotions = PromotionSector::where('sector_year_id', $sectorYear->id)
            ->get(['id', 'promotion_sector as name']);

        return response()->json($promotions);
    }

    /**
     * API : Classes d'une promotion
     */
    public function getClassesByPromotion($promotionId)
    {
        $classes = PromotionClassroom::where('promotion_sector_id', $promotionId)
            ->get(['id', 'name']);

        return response()->json($classes);

    }
        public function getByFilter($year, $sector, $promotion, $classroom)
    {
    try {
        $students = Student::select(
                'students.id',
                'students.matricule',
                'students.name',
                'students.surname',
                'students.sex',
                'students.birthday',
                'students.birthplace',
                'students.number',
                'students.aptitude'
            )
            ->join('recordings', 'students.id', '=', 'recordings.student_id')
            ->join('promotion_classrooms', 'recordings.classroom_id', '=', 'promotion_classrooms.id')
            ->where('recordings.year_id', $year)
            ->where('promotion_classrooms.sector_id', $sector)
            ->where('promotion_classrooms.promotion_sector_id', $promotion)
            ->where('recordings.classroom_id', $classroom)
            ->get();

        return response()->json(['data' => $students]);
    } catch (\Exception $e) {
        Log::error("Erreur getByFilter: " . $e->getMessage());
        return response()->json(['data' => []], 500);
    }
}
 public function import(Request $request)
    {
        $request->validate([
            'year_id' => 'required|exists:years,id',
            'sector_id' => 'required|exists:sectors,id',
            'promotion_id' => 'required|exists:promotion_sectors,id',
            'classroom_id' => 'required|exists:promotion_classrooms,id',
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new StudentsImport(
                $request->classroom_id,
                $request->year_id
            ), $request->file('file'));

            return back()->with('success', 'Liste des élèves importée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur import Excel élèves : " . $e->getMessage());
            return back()->withErrors('Erreur lors de l\'importation du fichier.');
        }
    }

}
