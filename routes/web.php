<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoteController;
use App\Http\Middleware\ForcePasswordChange;
use App\Http\Middleware\EnseignantMiddleware;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\PromotionClassroomController;
use App\Http\Controllers\PromotionSectorController;
use App\Http\Controllers\RatioController;
use App\Http\Controllers\SectorYearController;
use App\Http\Controllers\TeacherAssignmentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\YearController;

Route::get('/', fn() => redirect('login'));

Auth::routes(['register' => false]);

// ─── Parents ───────────────────────────────────────────────────────────────
Route::prefix('parents')->group(function () {
    Route::get('/',           [ParentController::class, 'index'])->name('parents.dashboard');
    Route::post('/results',   [ParentController::class, 'showResults'])->name('parents.results');
    Route::get('/export-pdf', [ParentController::class, 'exportPdf'])->name('parents.export.pdf');
    Route::get('/get-sectors/{yearId}',                          [ParentController::class, 'getSectors']);
    Route::get('/get-promotions/{yearId}/{sectorId}',            [ParentController::class, 'getPromotions']);
    Route::get('/get-classes/{yearId}/{sectorId}/{promotionId}', [ParentController::class, 'getClasses']);
});

// ─── Changement mot de passe ───────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get ('change-password', [PasswordChangeController::class, 'showChangeForm'])->name('password.change.form');
    Route::post('change-password', [PasswordChangeController::class, 'changePassword'])->name('password.change');
});

// ─── Zone principale ───────────────────────────────────────────────────────
// EnseignantMiddleware : si l'utilisateur est Enseignant et tente d'accéder
// à une route non autorisée, il est redirigé vers note.create.
// ──────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', ForcePasswordChange::class, EnseignantMiddleware::class])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::resource('sector',            SectorController::class);
    Route::resource('promotionbysector', PromotionSectorController::class);
    Route::resource('year',              YearController::class);
    Route::resource('ratio',             RatioController::class);
    Route::resource('subject',           SubjectController::class);
    Route::resource('sectorbyyear',      SectorYearController::class);
    Route::resource('student',           StudentController::class);
    Route::resource('user',              UserController::class);

    // ── Notes ──────────────────────────────────────────────────────────────
    Route::get('/note',           [NoteController::class, 'index'])->name('note.index');
    Route::get('/note/create',    [NoteController::class, 'create'])->name('note.create');
    Route::get('/note/{student}', [NoteController::class, 'show'])->name('note.show');

    Route::get('/notes/export',          [NoteController::class, 'export_view'])->name('export_view');
    Route::get('/notes/export/download', [NoteController::class, 'export'])->name('notes.export');

    Route::get('/bulletins',          [NoteController::class, 'getcards'])->name('get.cards');
    Route::get('/notes/export/fiche', [NoteController::class, 'exportcard'])->name('notes.exportcard');
    Route::get('/notes/fetch',        [NoteController::class, 'getStudentNotes'])->name('get.student.notes');

    // ── Années & Filières ──────────────────────────────────────────────────
    Route::get('disable/{year}',  [YearController::class, 'disableyear'])->name('disable_year');
    Route::get('activate/{year}', [YearController::class, 'activateyear'])->name('activate_year');
    Route::get('disable/{sector}',  [SectorController::class, 'disablesector'])->name('disable_sector');
    Route::get('activate/{sector}', [SectorController::class, 'activatesector'])->name('activate_sector');
    Route::get('/sectors/by-year/{year}',                    [SectorYearController::class,     'getSectorsByYear'])->name('sectors.byYear');
    Route::get('/sectors-by-year/{year}',                    [SectorController::class,         'getSectorsByYear']);
    Route::get('/promotions-by-sector/{sector}/year/{year}', [SectorController::class,         'getPromotionsBySectorAndYear']);
    Route::get('/promotion-sectors/{year}',                  [PromotionSectorController::class, 'getSectorsByYear'])->name('promotion-sectors.byYear');

    // ── Classes ────────────────────────────────────────────────────────────
    Route::get ('/classes/create', [PromotionClassroomController::class, 'create'])->name('promotion-classrooms.create');
    Route::post('/classes/store',  [PromotionClassroomController::class, 'store'])->name('promotion-classrooms.store');
    Route::get('/api/classroom-sectors-by-year/{yearId}',       [PromotionClassroomController::class, 'getSectorsByYear']);
    Route::get('/api/classroom-promotions/{yearId}/{sectorId}', [PromotionClassroomController::class, 'getPromotions']);
    Route::get('/api/existing-classrooms/{yearId}/{sectorId}',       [PromotionClassroomController::class, 'getExistingClassrooms']); 

    // ── Matières ───────────────────────────────────────────────────────────
    Route::get('/api/subject-sectors-by-year/{yearId}',       [SubjectController::class, 'getSectorsByYear']);
    Route::get('/api/subject-promotions/{yearId}/{sectorId}', [SubjectController::class, 'getPromotionsByYearSector']);
    Route::get('/api/promotion-classrooms/{year}/{sector}',   [SubjectController::class, 'getClassroomsByYearSector']);
    Route::get('/api/old-subjects/{oldYear}/{sector}',        [SubjectController::class, 'getOldSubjects']);

    // ── Ratios ─────────────────────────────────────────────────────────────
    Route::get('/api/ratios/sectors/{yearId}',               [RatioController::class, 'getSectorsByYear']);
    Route::get('/api/ratios/promotions/{yearId}/{sectorId}', [RatioController::class, 'getPromotionsByYearAndSector']);
    Route::get('/api/ratios/data/{promotionId}/{yearId}',    [RatioController::class, 'getSubjectsAndClasses']);

    // ── Étudiants ──────────────────────────────────────────────────────────
    Route::post('/import', [StudentController::class, 'import'])->name('import');
    Route::get('/students/{year}/{sector}/{promotion}/{classroom}', [StudentController::class, 'getByFilter']);

    // ── Affectations matière/classe ────────────────────────────────────────
    Route::prefix('teacher-assignments')->group(function () {
        Route::get   ('/',                                                 [TeacherAssignmentController::class, 'index'])->name('teacher-assignments.index');
        Route::post  ('/',                                                 [TeacherAssignmentController::class, 'store'])->name('teacher-assignments.store');
        Route::get   ('/get-sectors/{year}',                              [TeacherAssignmentController::class, 'getSectors']);
        Route::get   ('/get-promotions/{yearId}/{sectorId}',              [TeacherAssignmentController::class, 'getPromotions']);
        Route::get   ('/get-subjects/{yearId}/{sectorId}/{promotionId?}', [TeacherAssignmentController::class, 'getSubjects']);
        Route::get   ('/get-classes/{yearId}/{sectorId}/{promotionId?}',  [TeacherAssignmentController::class, 'getClasses']);
        Route::get   ('/get-assignments/{yearId}/{classroomId}',          [TeacherAssignmentController::class, 'getAssignments']);
        Route::delete('/{id}',                                            [TeacherAssignmentController::class, 'destroy']);
    });

    // ── Professeurs principaux ─────────────────────────────────────────────
    Route::prefix('principal-teachers')->group(function () {
        Route::get ('/',                            [TeacherAssignmentController::class, 'principalTeachers'])->name('principal-teachers.index');
        Route::post('/set',                         [TeacherAssignmentController::class, 'setPrincipal'])->name('principal-teachers.set');
        Route::post('/remove',                      [TeacherAssignmentController::class, 'removePrincipal'])->name('principal-teachers.remove');
        Route::get ('/get/{yearId}/{classroomId}',  [TeacherAssignmentController::class, 'getPrincipal']);
        Route::get ('/classes/{yearId}/{sectorId}', [TeacherAssignmentController::class, 'getClassesWithPrincipal']);
    });

    // ── API notes (dropdowns filtrés + saisie) ────────────────────────────
    Route::get ('/api/sectors-by-year/{yearId}',                      [NoteController::class, 'getSectorsByYear']);
    Route::get ('/api/promotions-by-year-sector/{yearId}/{sectorId}', [NoteController::class, 'getPromotionsByYearSector']);
    Route::get ('/api/classes-by-promotion/{promotionId}',            [NoteController::class, 'getClassesByPromotion']);
    Route::post('/api/subjects-by-classroom',                         [NoteController::class, 'getSubjectsByClassroom']);
    Route::post('/api/students-with-notes',                           [NoteController::class, 'getStudentsWithNotes']);
    Route::post('/api/notes/bulk',                                     [NoteController::class, 'storeBulk']);

    // ── Verrouillage notes (censeur/admin uniquement) ──────────────────────
    Route::post('/api/notes/toggle-lock', [NoteController::class, 'toggleLock'])->name('notes.toggleLock');
});
