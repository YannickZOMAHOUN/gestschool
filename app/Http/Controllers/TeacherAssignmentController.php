<?php

namespace App\Http\Controllers;

use App\Models\ClassSubjectTeacher;
use App\Models\PrincipalClassTeacher;
use App\Models\PromotionClassroom;
use App\Models\PromotionSector;
use App\Models\PromotionSubject;
use App\Models\SectorYear;
use App\Models\User;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * TeacherAssignmentController
 *
 * Gère deux concepts distincts :
 *
 * 1. AFFECTATION MATIÈRE (class_subject_teachers)
 *    → Un prof enseigne une matière dans une classe pour une année.
 *    → Une matière ne peut avoir qu'UN seul prof par classe par année.
 *
 * 2. PROFESSEUR PRINCIPAL (principal_class_teachers)
 *    → Un seul PP par classe par année.
 *    → Le PP peut être un enseignant qui n'enseigne PAS dans cette classe.
 */
class TeacherAssignmentController extends Controller
{
    // =========================================================
    // VUES
    // =========================================================

    public function index(Request $request)
    {
        $years    = Year::where('status', true)->get();
        $teachers = User::whereHas('roles', fn($q) => $q->where('name', 'Enseignant'))->get();

        return view('dashboard.users.teacher-assignment', compact('years', 'teachers'));
    }

    public function principalTeachers()
    {
        $years    = Year::where('status', true)->get();
        $teachers = User::whereHas('roles', fn($q) => $q->where('name', 'Enseignant'))->get();

        return view('dashboard.users.principal-teachers', compact('years', 'teachers'));
    }

    // =========================================================
    // API — Dropdowns hiérarchiques (partagés)
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

    public function getSubjects($yearId, $sectorId, $promotionId = null)
    {
        $sectorYear = SectorYear::where('year_id', $yearId)
            ->where('sector_id', $sectorId)
            ->first();

        if (!$sectorYear) return response()->json([]);

        $promotionSector = PromotionSector::where('sector_year_id', $sectorYear->id)
            ->when($promotionId, fn($q) => $q->where('id', $promotionId))
            ->first();

        if (!$promotionSector) return response()->json([]);

        $subjects = PromotionSubject::where('promotion_sector_id', $promotionSector->id)
            ->with('subject')
            ->get()
            ->map(fn($ps) => [
                'id'   => $ps->subject->id,
                'name' => $ps->subject->name,
            ]);

        return response()->json($subjects);
    }

    public function getClasses($yearId, $sectorId, $promotionId = null)
    {
        $sectorYear = SectorYear::where('year_id', $yearId)
            ->where('sector_id', $sectorId)
            ->first();

        if (!$sectorYear) return response()->json([]);

        $promotionSector = PromotionSector::where('sector_year_id', $sectorYear->id)
            ->when($promotionId, fn($q) => $q->where('id', $promotionId))
            ->first();

        if (!$promotionSector) return response()->json([]);

        $classes = PromotionClassroom::where('promotion_sector_id', $promotionSector->id)
            ->get()
            ->map(fn($c) => [
                'id'   => $c->id,
                'name' => $c->name,
            ]);

        return response()->json($classes);
    }

    // =========================================================
    // AFFECTATION MATIÈRE — CRUD
    // =========================================================

    /**
     * Créer une affectation enseignant/matière/classe.
     * RÈGLE : une matière = un seul prof par classe par année.
     */
    public function store(Request $request)
    {
        $request->validate([
            'teacher_id'   => 'required|exists:users,id',
            'year_id'      => 'required|exists:years,id',
            'classroom_id' => 'required|exists:promotion_classrooms,id',
            'subject_id'   => 'required|exists:subjects,id',
        ]);

        // Vérifier unicité matière/classe/année
        $exists = ClassSubjectTeacher::where('year_id',      $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->where('subject_id',   $request->subject_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Cette matière est déjà attribuée à un enseignant dans cette classe pour cette année.',
            ], 422);
        }

        $assignment = ClassSubjectTeacher::create([
            'user_id'      => $request->teacher_id,
            'classroom_id' => $request->classroom_id,
            'subject_id'   => $request->subject_id,
            'year_id'      => $request->year_id,
        ]);

        $assignment->load('user', 'subject');

        return response()->json([
            'success'    => true,
            'assignment' => [
                'id'      => $assignment->id,
                'subject' => $assignment->subject->name,
                'teacher' => $assignment->user->name . ' ' . $assignment->user->surname,
            ],
            'message' => 'Affectation enregistrée avec succès.',
        ]);
    }

    public function getAssignments($yearId, $classroomId)
    {
        $assignments = ClassSubjectTeacher::with(['user', 'subject'])
            ->where('year_id',      $yearId)
            ->where('classroom_id', $classroomId)
            ->get()
            ->map(fn($a) => [
                'id'      => $a->id,
                'subject' => $a->subject->name,
                'teacher' => $a->user->name . ' ' . $a->user->surname,
            ]);

        $pp = PrincipalClassTeacher::with('user')
            ->where('classroom_id', $classroomId)
            ->where('year_id',      $yearId)
            ->first();

        return response()->json([
            'assignments' => $assignments,
            'principal'   => $pp ? [
                'user_id' => $pp->user_id,
                'name'    => $pp->user->name . ' ' . $pp->user->surname,
            ] : null,
        ]);
    }

    public function destroy($id)
    {
        $assignment = ClassSubjectTeacher::findOrFail($id);
        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Affectation supprimée avec succès.',
        ]);
    }

    // =========================================================
    // PROFESSEUR PRINCIPAL
    // =========================================================

    /**
     * Définir/remplacer le PP d'une classe pour une année.
     * Le PP peut ne PAS enseigner dans cette classe.
     */
    public function setPrincipal(Request $request)
    {
        $request->validate([
            'teacher_id'   => 'required|exists:users,id',
            'classroom_id' => 'required|exists:promotion_classrooms,id',
            'year_id'      => 'required|exists:years,id',
        ]);

        $teacher = User::findOrFail($request->teacher_id);
        if (!$teacher->isEnseignant()) {
            return response()->json([
                'success' => false,
                'message' => 'Seul un enseignant peut être désigné professeur principal.',
            ], 422);
        }

        PrincipalClassTeacher::updateOrCreate(
            [
                'classroom_id' => $request->classroom_id,
                'year_id'      => $request->year_id,
            ],
            ['user_id' => $request->teacher_id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Professeur principal défini avec succès.',
        ]);
    }

    public function removePrincipal(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:promotion_classrooms,id',
            'year_id'      => 'required|exists:years,id',
        ]);

        PrincipalClassTeacher::where('classroom_id', $request->classroom_id)
            ->where('year_id', $request->year_id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Professeur principal retiré.']);
    }

    /**
     * Toutes les classes d'un secteur/année avec leur PP.
     */
    public function getClassesWithPrincipal($yearId, $sectorId)
    {
        $sectorYear = SectorYear::where('year_id',  $yearId)
            ->where('sector_id', $sectorId)
            ->first();

        if (!$sectorYear) return response()->json([]);

        $promotionSectors = PromotionSector::where('sector_year_id', $sectorYear->id)->get();

        $result = [];
        foreach ($promotionSectors as $ps) {
            foreach (PromotionClassroom::where('promotion_sector_id', $ps->id)->get() as $classroom) {
                $pp = PrincipalClassTeacher::with('user')
                    ->where('classroom_id', $classroom->id)
                    ->where('year_id',      $yearId)
                    ->first();

                $result[] = [
                    'classroom_id'   => $classroom->id,
                    'classroom_name' => $classroom->name,
                    'promotion'      => $ps->promotion_sector,
                    'principal'      => $pp ? [
                        'user_id' => $pp->user_id,
                        'name'    => $pp->user->name . ' ' . $pp->user->surname,
                    ] : null,
                ];
            }
        }

        return response()->json($result);
    }
}
