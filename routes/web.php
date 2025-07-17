<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\ForcePasswordChange;
use App\Http\Middleware\RoleMiddleware;

Route::get('/', function () { return redirect('login');});

Auth::routes(['register'=>false]);

Route::middleware(['auth','role:Parent'])->group(function () {
    Route::get('/parent/dashboard', [\App\Http\Controllers\ParentController::class, 'index'])->name('parent.dashboard');
});

// Routes accessibles dès l’authentification (même si mot de passe à changer)
Route::middleware('auth')->group(function () {
    Route::get('change-password', [PasswordChangeController::class, 'showChangeForm'])->name('password.change.form');
    Route::post('change-password', [PasswordChangeController::class, 'changePassword'])->name('password.change');
});

// Routes protégées, uniquement accessibles si connecté ET mot de passe changé
Route::middleware(['auth', ForcePasswordChange::class])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::resource('sector', \App\Http\Controllers\SectorController::class);
    Route::resource('promotionbysector', \App\Http\Controllers\PromotionSectorController::class);
    Route::resource('year', \App\Http\Controllers\YearController::class);
    Route::resource('ratio', \App\Http\Controllers\RatioController::class);
    Route::resource('subject', \App\Http\Controllers\SubjectController::class);
    Route::resource('sectorbyyear', \App\Http\Controllers\SectorYearController::class);
    Route::resource('student', \App\Http\Controllers\StudentController::class);
    Route::resource('note', \App\Http\Controllers\NoteController::class);
    Route::get('disable/{year}', [\App\Http\Controllers\YearController::class, 'disableyear'])->name('disable_year');
    Route::get('activate/{year}', [\App\Http\Controllers\YearController::class, 'activateyear'])->name('activate_year');
    Route::get('disable/{sector}', [\App\Http\Controllers\SectorController::class, 'disablesector'])->name('disable_sector');
    Route::get('activate/{sector}', [\App\Http\Controllers\SectorController::class, 'activatesector'])->name('activate_sector');
    Route::get('/sectors/by-year/{year}', [\App\Http\Controllers\SectorYearController::class, 'getSectorsByYear'])->name('sectors.byYear');
    Route::get('/sectors-by-year/{year}', [\App\Http\Controllers\SectorController::class, 'getSectorsByYear']);
    Route::get('/promotions-by-sector/{sector}/year/{year}', [\App\Http\Controllers\SectorController::class, 'getPromotionsBySectorAndYear']);
    Route::get('/promotion-sectors/{year}', [\App\Http\Controllers\PromotionSectorController::class, 'getSectorsByYear'])->name('promotion-sectors.byYear');
    Route::get('/classes/create', [\App\Http\Controllers\PromotionClassroomController::class, 'create'])->name('promotion-classrooms.create');
    Route::post('/classes/store', [\App\Http\Controllers\PromotionClassroomController::class, 'store'])->name('promotion-classrooms.store');
    Route::get('/api/classroom-sectors-by-year/{yearId}', [\App\Http\Controllers\PromotionClassroomController::class, 'getSectorsByYear']);
    Route::get('/api/classroom-promotions/{yearId}/{sectorId}', [\App\Http\Controllers\PromotionClassroomController::class, 'getPromotions']);
    Route::get('/api/subject-sectors-by-year/{year}', [\App\Http\Controllers\SubjectController::class, 'getSectorsByYear']);
    Route::get('/api/subject-promotions/{yearId}/{sectorId}', [\App\Http\Controllers\SubjectController::class, 'getPromotionsByYearSector']);
    Route::get('/api/promotion-classrooms/{year}/{sector}', [\App\Http\Controllers\SubjectController::class, 'getClassroomsByYearSector']);
    Route::get('/api/old-subjects/{oldYear}/{sector}', [\App\Http\Controllers\SubjectController::class, 'getOldSubjects']);
    Route::get('/subjects/create', [\App\Http\Controllers\SubjectController::class, 'create'])->name('subject.create');
    Route::post('/subjects/store', [\App\Http\Controllers\SubjectController::class, 'store'])->name('subject.store');
    Route::get('/api/ratios/sectors/{yearId}', [\App\Http\Controllers\RatioController::class, 'getSectorsByYear']);
    Route::get('/api/ratios/promotions/{yearId}/{sectorId}', [\App\Http\Controllers\RatioController::class, 'getPromotionsByYearAndSector']);
    Route::get('/api/ratios/data/{promotionId}/{yearId}', [\App\Http\Controllers\RatioController::class, 'getSubjectsAndClasses']);
    Route::post('/import', [\App\Http\Controllers\StudentController::class, 'import'])->name('import');
    Route::get('/students/{year}/{sector}/{promotion}/{classroom}', [\App\Http\Controllers\StudentController::class, 'getByFilter']);
    Route::get('/api/sectors-by-year/{yearId}', [\App\Http\Controllers\StudentController::class, 'getSectorsByYear']);
    Route::get('/api/promotions-by-year-sector/{yearId}/{sectorId}', [\App\Http\Controllers\StudentController::class, 'getPromotionsByYearSector']);
    Route::get('/api/classes-by-promotion/{promotionId}', [\App\Http\Controllers\StudentController::class, 'getClassesByPromotion']);
    Route::get('/api/students-by-class/{classroom}/{year}', [\App\Http\Controllers\NoteController::class, 'getStudents']);
    Route::get('/api/subjects-with-ratios/{classroom}/{year}', [\App\Http\Controllers\NoteController::class, 'getSubjectsWithRatios']);
    Route::post('/api/notes/existing', [\App\Http\Controllers\NoteController::class, 'getExistingNotes']);
    Route::get('/notes/export/download', [\App\Http\Controllers\NoteController::class, 'export'])->name('notes.export');
    Route::get('/notes/export', [\App\Http\Controllers\NoteController::class, 'export_view'])->name('export_view');
    Route::get('/notes/fetch', [\App\Http\Controllers\NoteController::class, 'getStudentNotes'])->name('get.student.notes');
    Route::get('/note/{student}', [\App\Http\Controllers\NoteController::class, 'show'])->name('note.show');

    Route::get('/api/notes-by-class-semester/{classroom_id}/{year_id}/{semester}', [\App\Http\Controllers\NoteController::class, 'byClassAndSemester']);
    Route::put('/api/notes/{note}', [\App\Http\Controllers\NoteController::class, 'update']);

    Route::get('/bulletins', [\App\Http\Controllers\NoteController::class, 'getcards'])->name('get.cards');
    Route::get('/notes/export/fiche', [\App\Http\Controllers\NoteController::class, 'exportcard'])->name('notes.exportcard');

});

