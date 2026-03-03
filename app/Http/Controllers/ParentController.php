<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\PromotionClassroom;
use App\Models\PromotionSector;
use App\Models\Ratio;
use App\Models\Recording;
use App\Models\Sector;
use App\Models\SectorYear;
use App\Models\Student;
use App\Models\Year;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ParentController extends Controller
{
    // =========================================================
    // HELPER — Troncage à 2 décimales SANS arrondi
    // Identique à NoteController pour cohérence des calculs
    // =========================================================
    private function trunc2(?float $val): ?float
    {
        if ($val === null) return null;
        return floor($val * 100) / 100;
    }

    /**
     * Calcul de la moyenne /20 d'une matière.
     *
     * Règle (identique à NoteController::calculateMoyenne20) :
     *   1. Moyenne des interros = moyenne arithmétique des interros présentes (null si aucune)
     *   2. Composantes = [moy_interros?, devoir1?, devoir2?]  — seules les valeurs non-null entrent
     *   3. Moyenne /20 = moyenne arithmétique des composantes présentes
     *   4. Retourne null si AUCUNE composante n'est renseignée
     *
     * CORRECTIONS par rapport à l'ancienne version :
     *   ❌ Ancien : interrosAverage = 0 quand pas d'interros → faussait la moyenne
     *   ✅ Nouveau : interrosAverage = null → n'entre pas dans le calcul
     *
     *   ❌ Ancien : devoir1/devoir2 initialisés à 0 par défaut → faussaient la moyenne
     *   ✅ Nouveau : null si absent → n'entre pas dans le calcul
     *
     *   ❌ Ancien : division toujours par 3 (ou 2) → mauvais si composantes manquantes
     *   ✅ Nouveau : division par count($composantes) → exact quel que soit le nombre de notes saisies
     */
    private function calculateMoyenne20(array $interros, $devoir1, $devoir2): ?float
    {
        $moyInterros = count($interros) > 0
            ? array_sum($interros) / count($interros)
            : null;

        $d1 = ($devoir1 !== null && $devoir1 !== '') ? floatval($devoir1) : null;
        $d2 = ($devoir2 !== null && $devoir2 !== '') ? floatval($devoir2) : null;

        $composantes = [];
        if ($moyInterros !== null) $composantes[] = $moyInterros;
        if ($d1 !== null)          $composantes[] = $d1;
        if ($d2 !== null)          $composantes[] = $d2;

        if (count($composantes) === 0) return null;
        return $this->trunc2(array_sum($composantes) / count($composantes));
    }

    // =========================================================
    // PAGE D'ACCUEIL — Formulaire de recherche
    // =========================================================

    public function index()
    {
        try {
            $years = Year::where('status', true)->orderBy('year', 'desc')->get();
            return view('parents.dashboard', compact('years'));
        } catch (\Exception $e) {
            Log::error('ParentController::index — ' . $e->getMessage());
            return back()->withErrors('Une erreur est survenue lors du chargement.');
        }
    }

    // =========================================================
    // API — Dropdowns en cascade (accès public non authentifié)
    // =========================================================

    public function getSectors($yearId)
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

    public function getPromotions($yearId, $sectorId)
    {
        $sectorYear = SectorYear::where('year_id', $yearId)
            ->where('sector_id', $sectorId)
            ->first();

        if (!$sectorYear) {
            return response()->json([]);
        }

        $promotions = PromotionSector::where('sector_year_id', $sectorYear->id)
            ->get(['id', 'promotion_sector as name']);

        return response()->json($promotions);
    }

    public function getClasses($yearId, $sectorId, $promotionId)
    {
        $sectorYear = SectorYear::where('year_id', $yearId)
            ->where('sector_id', $sectorId)
            ->first();

        if (!$sectorYear) return response()->json([]);

        $promotionSector = PromotionSector::where('sector_year_id', $sectorYear->id)
            ->where('id', $promotionId)
            ->first();

        if (!$promotionSector) return response()->json([]);

        $classes = PromotionClassroom::where('promotion_sector_id', $promotionSector->id)
            ->get()
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name]);

        return response()->json($classes);
    }

    // =========================================================
    // AFFICHAGE DES RÉSULTATS
    // =========================================================

    public function showResults(Request $request)
    {
        $years = Year::where('status', true)->orderBy('year', 'desc')->get();

        if (!$request->filled('year_id')) {
            return view('parents.dashboard', compact('years'));
        }

        $request->validate([
            'year_id'      => 'required|exists:years,id',
            'classroom_id' => 'required|exists:promotion_classrooms,id',
            'semester'     => 'required|in:1,2',
            'matricule'    => 'required|string|max:50',
        ]);

        // Trouver l'élève
        $student = Student::where('matricule', $request->matricule)->first();
        if (!$student) {
            return back()->withInput()->withErrors([
                'matricule' => 'Aucun élève trouvé avec ce matricule.',
            ]);
        }

        // Vérifier l'inscription dans cette classe/année
        $recording = Recording::where('student_id',  $student->id)
            ->where('year_id',      $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->first();

        if (!$recording) {
            return back()->withInput()->withErrors([
                'matricule' => 'Cet élève n\'est pas inscrit dans cette classe pour cette année scolaire.',
            ]);
        }

        $data = $this->buildResultsData($request, $student, $recording);

        return view('parents.dashboard', array_merge(compact('years'), $data, [
            'request'   => $request,
            'year'      => Year::find($request->year_id),
            'classroom' => PromotionClassroom::find($request->classroom_id),
        ]));
    }

    // =========================================================
    // EXPORT PDF
    // =========================================================

    public function exportPdf(Request $request)
    {
        $request->validate([
            'year_id'      => 'required|exists:years,id',
            'classroom_id' => 'required|exists:promotion_classrooms,id',
            'semester'     => 'required|in:1,2',
            'matricule'    => 'required|string|max:50',
        ]);

        $student = Student::where('matricule', $request->matricule)->firstOrFail();

        $recording = Recording::where('student_id',  $student->id)
            ->where('year_id',      $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->firstOrFail();

        $data = $this->buildResultsData($request, $student, $recording);

        $data['request']   = $request;
        $data['year']      = Year::find($request->year_id);
        $data['classroom'] = PromotionClassroom::find($request->classroom_id);
        $data['sector']    = Sector::find($request->sector_id);
        $data['promotion'] = PromotionSector::find($request->promotion_id);

        $pdf = Pdf::loadView('parents.results-pdf', $data)
            ->setPaper('a4', 'portrait');

        $filename = 'resultats-' . $student->matricule . '-S' . $request->semester . '.pdf';

        return $pdf->download($filename);
    }

    // =========================================================
    // HELPER PRIVÉ — Construction des données de résultats
    // =========================================================

    private function buildResultsData(Request $request, Student $student, Recording $recording): array
    {
        // Toutes les matières de cette classe pour cette année
        $ratios = Ratio::with('subject')
            ->where('year_id',      $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->get();

        $results          = [];
        $totalCoefficient = 0;
        $totalWeighted    = 0;

        foreach ($ratios as $ratio) {
            $note = Note::where('recording_id', $recording->id)
                ->where('subject_id', $ratio->subject_id)
                ->where('semester',   $request->semester)
                ->first();

            // ✅ Normaliser les interros (JSON ou tableau) + filtrer les valeurs non numériques
            $interros = [];
            if ($note && $note->interros) {
                $raw      = is_array($note->interros)
                    ? $note->interros
                    : (json_decode($note->interros, true) ?? []);
                $interros = array_values(array_filter($raw, 'is_numeric'));
            }

            // ✅ null si absent (pas 0 — évite de fausser le calcul)
            $devoir1 = ($note && $note->devoir1 !== null && $note->devoir1 !== '') ? $note->devoir1 : null;
            $devoir2 = ($note && $note->devoir2 !== null && $note->devoir2 !== '') ? $note->devoir2 : null;

            // ✅ Calcul identique à NoteController
            $moyInterros  = count($interros) > 0
                ? $this->trunc2(array_sum($interros) / count($interros))
                : null;

            $subjectAvg  = $this->calculateMoyenne20($interros, $devoir1, $devoir2);

            // Moyenne pondérée = moy/20 × coefficient (null si pas de note)
            $weightedAvg = ($subjectAvg !== null)
                ? $this->trunc2($subjectAvg * $ratio->coefficient)
                : null;

            $results[] = [
                'subject'          => $ratio->subject->name,
                'coefficient'      => $ratio->coefficient,
                'interros'         => $interros,
                'moy_interros'     => $moyInterros,
                'devoir1'          => $devoir1,
                'devoir2'          => $devoir2,
                'subject_average'  => $subjectAvg,
                'weighted_average' => $weightedAvg,
            ];

            // ✅ N'additionner que les matières avec une note effective
            if ($weightedAvg !== null) {
                $totalCoefficient += $ratio->coefficient;
                $totalWeighted    += $weightedAvg;
            }
        }

        // ✅ Moyenne générale tronquée (pas arrondie)
        $generalAverage = $totalCoefficient > 0
            ? $this->trunc2($totalWeighted / $totalCoefficient)
            : null;

        // Décision de passage (semestre 2 uniquement, null sinon)
        $isPassed = ($request->semester == 2 && $generalAverage !== null)
            ? $generalAverage >= 10
            : null;

        return compact('student', 'results', 'generalAverage', 'isPassed');
    }
}
