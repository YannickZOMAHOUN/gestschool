<?php

// app/Http/Controllers/TeacherAssignmentController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PromotionClassroom;
use App\Models\Subject;
use App\Models\Year;
use App\Models\ClassSubjectTeacher;
use App\Models\PromotionSector;
use App\Models\PromotionSubject;
use App\Models\Ratio;
use App\Models\Sector;
use App\Models\SectorYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeacherAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $years = Year::where('status', true)->get();
        $data = [
            'years' => $years,
            'teachers' => User::whereHas('roles', fn($q) => $q->where('name', 'Enseignant'))->get(),
        ];
        return view('dashboard.users.teacher-assignment', $data);

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
   public function getSubjects($yearId, $sectorId, $promotionId = null)
    {
        // Étape 1 : récupérer le sector_year
        $sectorYear = SectorYear::where('year_id', $yearId)
            ->where('sector_id', $sectorId)
            ->first();

        if (!$sectorYear) {
            return response()->json([]);
        }

        // Étape 2 : récupérer le promotion_sector correspondant à la promotion sélectionnée
        $promotionSector = PromotionSector::where('sector_year_id', $sectorYear->id)
            ->when($promotionId, function($query) use ($promotionId) {
                return $query->where('id', $promotionId);
            })
            ->first();

        if (!$promotionSector) {
            return response()->json([]);
        }

        // Étape 3 : récupérer les matières via PromotionSubject
        $subjects = PromotionSubject::where('promotion_sector_id', $promotionSector->id)
            ->with('subject')
            ->get()
            ->map(function ($ps) {
                return [
                    'id' => $ps->subject->id,
                    'name' => $ps->subject->name,
                ];
            });

        Log::info("Subjects found for promotion {$promotionSector->id}:", $subjects->toArray());
        return response()->json($subjects);
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

        Log::info("Classrooms found for promotion {$promotionSector->id}:", $classes->toArray());
        return response()->json($classes);
    }
    public function principalTeachers()
    {
       $years = Year::where('status', true)->get();
        $data = [
            'years' => $years,
                ];

        return view('dashboard.users.principal-teachers', $data);
    }
    public function setPrincipalTeacher(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:promotion_classrooms,id',
            'teacher_id' => 'required|exists:users,id'
        ]);

        // D'abord, retirer le statut de prof principal pour cette classe
        ClassSubjectTeacher::where('classroom_id', $request->classroom_id)
                         ->update(['is_principal' => false]);

        // Ensuite, assigner le nouveau prof principal
        ClassSubjectTeacher::where('user_id', $request->teacher_id)
                         ->where('classroom_id', $request->classroom_id)
                         ->update(['is_principal' => true]);

        return back()->with('success', 'Professeur principal assigné avec succès');
    }
    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'year_id' => 'required|exists:years,id',
            'classroom_id' => 'required|exists:promotion_classrooms,id',
            'subject_id' => 'required|exists:subjects,id'
        ]);

        // Convertir la valeur du checkbox en booléen
        $isPrincipal = $request->has('is_principal') ? true : false;

        // Vérification de l'unicité
        $exists = ClassSubjectTeacher::where('year_id', $request->year_id)
            ->where('classroom_id', $request->classroom_id)
            ->where('subject_id', $request->subject_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Cette matière est déjà attribuée à cette classe pour cette année'
            ], 422);
        }

        $assignment = ClassSubjectTeacher::create([
            'user_id' => $request->teacher_id,
            'classroom_id' => $request->classroom_id,
            'subject_id' => $request->subject_id,
            'year_id' => $request->year_id,
            'is_principal' => $isPrincipal // Utilisation de la valeur convertie
        ]);

        // Charger les relations pour la réponse
        $assignment->load('user', 'subject');

        return response()->json([
            'success' => true,
            'assignment' => [
                'id' => $assignment->id,
                'subject' => $assignment->subject->name,
                'teacher' => $assignment->user->name . ' ' . $assignment->user->surname,
                'is_principal' => $assignment->is_principal
            ],
            'message' => 'Affectation enregistrée avec succès'
        ]);
    }
    public function getAssignments($yearId, $classroomId)
    {
        $assignments = ClassSubjectTeacher::with(['user', 'subject'])
            ->where('year_id', $yearId)
            ->where('classroom_id', $classroomId)
            ->get()
            ->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'subject' => $assignment->subject->name,
                    'teacher' => $assignment->user->name . ' ' . $assignment->user->surname,
                    'is_principal' => $assignment->is_principal
                ];
            });

        return response()->json($assignments);
    }
    public function destroy($id)
    {
        $assignment = ClassSubjectTeacher::findOrFail($id);

        // Vérifier si c'est un PP avant de supprimer
        $wasPrincipal = $assignment->is_principal;
        $classroomId = $assignment->classroom_id;

        $assignment->delete();

        return response()->json([
            'success' => true,
            'was_principal' => $wasPrincipal,
            'classroom_id' => $classroomId,
            'message' => 'Affectation supprimée avec succès'
        ]);
    }
    public function setPrincipal(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:class_subject_teachers,id',
            'classroom_id' => 'required|exists:promotion_classrooms,id'
        ]);

        // D'abord, retirer le statut de prof principal pour cette classe
        ClassSubjectTeacher::where('classroom_id', $request->classroom_id)
                        ->update(['is_principal' => false]);

        // Ensuite, assigner le nouveau prof principal
        $assignment = ClassSubjectTeacher::find($request->assignment_id);
        $assignment->update(['is_principal' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Professeur principal défini avec succès'
        ]);
    }
}
