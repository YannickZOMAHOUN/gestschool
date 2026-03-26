<?php

namespace App\Http\Controllers;

use App\Models\ClassSubjectTeacher;
use App\Models\Note;
use App\Models\PromotionClassroom;
use App\Models\Ratio;
use App\Models\Recording;
use App\Models\User;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $now  = now();
        $user = Auth::user();

        // ── Année active ──────────────────────────────────────
        $activeYear = Year::orderBy('year', 'desc')->first();
        $yearId     = $activeYear?->id;

        // ── Stats filtrées sur l'année active ─────────────────

        $totalStudents = $yearId
            ? Recording::where('year_id', $yearId)
                ->distinct('student_id')
                ->count('student_id')
            : 0;

        $totalTeachers = $yearId
            ? ClassSubjectTeacher::where('year_id', $yearId)
                ->distinct('user_id')
                ->count('user_id')
            : 0;

        $totalClassrooms = $yearId
            ? PromotionClassroom::whereHas('promotionSector.sectorYear', fn($q) =>
                $q->where('year_id', $yearId)
              )->count()
            : 0;

        $totalSubjects = $yearId
            ? Ratio::where('year_id', $yearId)
                ->distinct('subject_id')
                ->count('subject_id')
            : 0;

        // ── Progression notes par filière ─────────────────────
        $filieresProgress = collect();

        if ($yearId) {
            $classrooms = PromotionClassroom::with([
                'promotionSector.sectorYear.sector',
            ])->whereHas('promotionSector.sectorYear', fn($q) =>
                $q->where('year_id', $yearId)
            )->get();

            $bySector = $classrooms->groupBy(fn($c) =>
                $c->promotionSector->sectorYear->sector->name_sector ?? 'Autre'
            );

            foreach ($bySector as $sectorName => $sectorClassrooms) {
                $totalExpected = 0;
                $totalSaved    = 0;

                foreach ($sectorClassrooms as $classroom) {
                    $classroomId = $classroom->id;

                    $nbStudents = Recording::where('year_id', $yearId)
                        ->where('classroom_id', $classroomId)
                        ->count();

                    $nbRatios = Ratio::where('year_id', $yearId)
                        ->where('classroom_id', $classroomId)
                        ->count();

                    $totalExpected += $nbStudents * $nbRatios;

                    $totalSaved += Note::whereHas('recording', fn($q) =>
                        $q->where('classroom_id', $classroomId)
                          ->where('year_id', $yearId)
                    )->count();
                }

                $pct = $totalExpected > 0
                    ? min(100, round(($totalSaved / $totalExpected) * 100))
                    : 0;

                $filieresProgress->push([
                    'name' => $sectorName,
                    'pct'  => $pct,
                ]);
            }

            $filieresProgress = $filieresProgress->sortByDesc('pct')->values();
        }

        $progressColors = [
            'var(--sg-accent)',
            'var(--sg-info)',
            'var(--sg-success)',
            'var(--sg-warn)',
            '#5d4a8a',
            '#a84040',
        ];

        // ── Derniers utilisateurs ─────────────────────────────
        $latestUsers = User::with('roles')->latest()->take(6)->get();

        // ── Répartition par rôle ──────────────────────────────
        $roleGroups = User::with('roles')->get()
            ->groupBy(fn($u) => optional($u->roles->first())->name ?? 'Sans rôle');

        $totalUsers = $roleGroups->sum(fn($g) => $g->count());

        $roleList = $roleGroups->map(fn($g, $name) => [
            'name'  => $name,
            'count' => $g->count(),
            'pct'   => $totalUsers > 0 ? round($g->count() / $totalUsers * 100) : 0,
        ])->values();

        $roleColors = [
            '#3a6b35', '#2a6090', '#9a6a1a', '#2e7d4f', '#a84040', '#5d4a8a',
        ];

        // ── Activité récente ──────────────────────────────────
        $recentNotes = collect();
        if ($yearId) {
            $recentNotes = Note::with([
                'recording.student',
                'recording.classroom',
                'subject',
            ])->whereHas('recording', fn($q) =>
                $q->where('year_id', $yearId)
            )->latest('updated_at')->take(5)->get();
        }

        // ── Avancement du semestre ────────────────────────────
        // Semestre 1 : septembre → janvier  |  Semestre 2 : février → juin
        $month = $now->month;

        if ($month >= 9) {
            $semStart = Carbon::createFromDate($now->year,     9,  15);
            $semEnd   = Carbon::createFromDate($now->year + 1, 1,  31);
            $semLabel = 'Semestre 1';
        } else {
            $semStart = Carbon::createFromDate($now->year, 2,  1);
            $semEnd   = Carbon::createFromDate($now->year, 6, 30);
            $semLabel = 'Semestre 2';
        }

        // Ces trois variables sont utilisées dans la vue — toutes passées via compact()
        $totalDays = max(1, (int) $semStart->diffInDays($semEnd));
        $elapsed   = (int) min($semStart->diffInDays($now), $totalDays);
        $daysLeft  = max(0, $totalDays - $elapsed);
        $semPct    = (int) round(($elapsed / $totalDays) * 100);

        return view('home', compact(
            'now',
            'user',
            'activeYear',
            'totalStudents',
            'totalTeachers',
            'totalClassrooms',
            'totalSubjects',
            'filieresProgress',
            'progressColors',
            'latestUsers',
            'roleList',
            'roleColors',
            'totalUsers',
            'recentNotes',
            'semLabel',
            'semStart',
            'semEnd',
            'semPct',
            'totalDays',   // ← était manquant
            'elapsed',     // ← était manquant
            'daysLeft',    // ← remplace $totalDays - $elapsed dans la vue
        ));
    }
}
