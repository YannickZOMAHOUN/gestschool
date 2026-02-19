<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\PromotionClassroom;
use App\Models\PromotionSector;
use App\Models\PromotionSubject;
use App\Models\Ratio;
use App\Models\Recording;
use App\Models\Sector;
use App\Models\SectorYear;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Year;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PDF;

class ParentController extends Controller
{
    public function index()
    {
        try{
        $years = Year::where('status', true)->get();
        return view('parents.dashboard', compact('years'));

        }catch (\Exception $e) {
            Log::error("Erreur " . $e->getMessage());
            return back()->withErrors('Erreur lors de l\'importation du fichier.');
        }

    }
    public function getSectors($yearId)
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
    public function getPromotions($yearId, $sectorId)
    {
        $sectorYear = SectorYear::where('year_id', $yearId)
                ->where('sector_id', $sectorId)
                ->first();

            if (!$sectorYear) {
                Log::info("No SectorYear found for year_id=$yearId and sector_id=$sectorId");
                return response()->json([]);
            }

            $promotions = PromotionSector::where('sector_year_id', $sectorYear->id)
                ->get(['id', 'promotion_sector as name']);

            Log::info("Promotions found:", $promotions->toArray());

            return response()->json($promotions);
    }
     public function getClasses($yearId, $sectorId, $promotionId = null)
    {
        // Étape 1 : retrouver l'entrée dans sector_years
        $sectorYear = SectorYear::where('year_id', $yearId)
            ->where('sector_id', $sectorId)
            ->first();

        if (!$sectorYear) {
            return response()->json([]);
        }

        // Étape 2 : retrouver l'entrée dans promotion_sectors correspondant à la promotion sélectionnée
        $promotionSector = PromotionSector::where('sector_year_id', $sectorYear->id)
            ->when($promotionId, function($query) use ($promotionId) {
                return $query->where('id', $promotionId);
            })
            ->first();

        if (!$promotionSector) {
            return response()->json([]);
        }

        // Étape 3 : retrouver les classes associées à cette promotion spécifique
        $classes = PromotionClassroom::where('promotion_sector_id', $promotionSector->id)
            ->get()
            ->map(function ($classroom) {
                return [
                    'id' => $classroom->id,
                    'name' => $classroom->name
                ];
            });
        return response()->json($classes);
    }
    public function showResults(Request $request)
    {
        $years = Year::where('status', true)->get(); // Conserver la liste des années

        // Si ce n'est pas une soumission de formulaire, afficher juste le formulaire
        if (!$request->filled('year_id')) {
            return view('parents.dashboard', compact('years'));
        }

        $request->validate([
            'year_id' => 'required|exists:years,id',
            'classroom_id' => 'required|exists:promotion_classrooms,id',
            'semester' => 'required|in:1,2',
            'matricule' => 'required|string'
        ]);

        // Trouver l'étudiant
        $student = Student::where('matricule', $request->matricule)->firstOrFail();

        // Vérifier que l'étudiant est bien dans cette classe cette année
        $recording = Recording::where('student_id', $student->id)
            ->where('year_id', $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->firstOrFail();

        // Récupérer toutes les matières de la classe avec leurs coefficients
        $subjects = Ratio::with('subject')
            ->where('year_id', $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->get();

        // Récupérer les notes pour chaque matière
        $results = [];
        $totalCoefficient = 0;
        $totalWeightedAverage = 0;

        foreach ($subjects as $subject) {
            $note = Note::where('recording_id', $recording->id)
                ->where('subject_id', $subject->subject_id)
                ->where('semester', $request->semester)
                ->first();

            $interros = $note ? ($note->interros ?? []) : [];
            $devoir1 = $note ? ($note->devoir1 ?? 0) : 0;
            $devoir2 = $note ? ($note->devoir2 ?? 0) : 0;

            // Calcul des moyennes
            $interrosAverage = count($interros) > 0 ? array_sum($interros) / count($interros) : 0;

            if (count($interros) > 0) {
                $subjectAverage = ($interrosAverage + $devoir1 + $devoir2) / 3;
            } else {
                $subjectAverage = ($devoir1 + $devoir2) / 2;
            }

            $weightedAverage = $subjectAverage * $subject->coefficient;

            $results[] = [
                'subject' => $subject->subject->name,
                'coefficient' => $subject->coefficient,
                'interros' => $interros,
                'interros_average' => $interrosAverage,
                'subject_average' => $subjectAverage,
                'weighted_average' => $weightedAverage,
                'devoir1'=>$devoir1,
                'devoir2'=>$devoir2,
            ];

            $totalCoefficient += $subject->coefficient;
            $totalWeightedAverage += $weightedAverage;
        }

        $generalAverage = $totalCoefficient > 0 ? $totalWeightedAverage / $totalCoefficient : 0;
        $isPassed = $request->semester == 2 ? $generalAverage >= 10 : null;
        $year = Year::find($request->year_id);
        $classroom = PromotionClassroom::find($request->classroom_id);


        return view('parents.dashboard', compact(
            'years',
            'student',
            'results',
            'generalAverage',
            'isPassed',
            'request',
            'year',
            'classroom',
        ));
    }
    public function exportPdf(Request $request)
    {
        // Récupérer les données comme dans showResults
        $data = $this->getResultsData($request);

        // Charger la vue PDF
        $pdf = PDF::loadView('parents.results-pdf', $data);
        return $pdf->download('resultats-'.$data['student']->surname.'_'.$data['student']->name.'.pdf');
    }
    protected function getResultsData(Request $request)
    {
        // Même logique que showResults mais retourne un array
        $student = Student::where('matricule', $request->matricule)->firstOrFail();

        $recording = Recording::where('student_id', $student->id)
            ->where('year_id', $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->firstOrFail();

        $subjects = Ratio::with('subject')
            ->where('year_id', $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->get();

        $results = [];
        $totalCoefficient = 0;
        $totalWeightedAverage = 0;

        foreach ($subjects as $subject) {
            $note = Note::where('recording_id', $recording->id)
                ->where('subject_id', $subject->subject_id)
                ->where('semester', $request->semester)
                ->first();

            $interros = $note ? ($note->interros ?? []) : [];
            $devoir1 = $note ? ($note->devoir1 ?? 0) : 0;
            $devoir2 = $note ? ($note->devoir2 ?? 0) : 0;

            // Calcul des moyennes
            $interrosAverage = count($interros) > 0 ? array_sum($interros) / count($interros) : 0;

            if (count($interros) > 0) {
                $subjectAverage = ($interrosAverage + $devoir1 + $devoir2) / 3;
            } else {
                $subjectAverage = ($devoir1 + $devoir2) / 2;
            }

            $weightedAverage = $subjectAverage * $subject->coefficient;

            $results[] = [
                'subject' => $subject->subject->name,
                'coefficient' => $subject->coefficient,
                'interros' => $interros,
                'interros_average' => $interrosAverage,
                'subject_average' => $subjectAverage,
                'weighted_average' => $weightedAverage,
                'devoir1'=>$devoir1,
                'devoir2'=>$devoir2,
            ];

            $totalCoefficient += $subject->coefficient;
            $totalWeightedAverage += $weightedAverage;
        }
        $generalAverage = $totalCoefficient > 0 ? $totalWeightedAverage / $totalCoefficient : 0;
        $isPassed = $request->semester == 2 ? $generalAverage >= 10 : null;

        return [
            'student' => $student,
            'results' => $results,
            'generalAverage' => $generalAverage,
            'isPassed' => $isPassed,
            'request' => $request,
            'year' => Year::find($request->year_id),
            'classroom' => PromotionClassroom::find($request->classroom_id),
            'sector' => Sector::find($request->sector_id),
            'promotion' => PromotionSector::find($request->promotion_id)
        ];
    }
}
