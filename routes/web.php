<?php

use App\Http\Controllers\Auth\CompanyAuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Company\DashboardController as CompanyDashboard;
use App\Http\Controllers\Company\JobPostingController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\JobController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ─── Página principal pública ──────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ─── Autenticación ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    // Login general (empresa + admin)
    Route::get('/login',    [CompanyAuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [CompanyAuthController::class, 'login']);

    // Registro de empresa
    Route::get('/register/empresa',   [CompanyAuthController::class, 'showRegister'])->name('company.register');
    Route::post('/register/empresa',  [CompanyAuthController::class, 'register'])->name('company.register.store');

    // Google OAuth (estudiantes)
    Route::get('/auth/google',          [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

// Logout
Route::post('/logout', [CompanyAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Marcar notificación como leída
Route::get('/notifications/{id}/read', function ($id) {
    auth()->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
    return response()->json(['ok' => true]);
})->middleware('auth');

// ─── Panel de ADMINISTRADOR ────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard',                [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/empresas',                 [AdminDashboard::class, 'companies'])->name('companies');
    Route::post('/empresas/{company}/aprobar', [AdminDashboard::class, 'approveCompany'])->name('companies.approve');
    Route::post('/empresas/{company}/rechazar', [AdminDashboard::class, 'rejectCompany'])->name('companies.reject');
    Route::get('/usuarios',                 [AdminDashboard::class, 'users'])->name('users');
    Route::post('/usuarios/{user}/toggle',  [AdminDashboard::class, 'toggleUser'])->name('users.toggle');
    Route::get('/reportes',                 [AdminDashboard::class, 'reports'])->name('reports');
});

// ─── Panel de EMPRESA ──────────────────────────────────────────────────────────
Route::prefix('empresa')->name('company.')->middleware(['auth', 'role:company'])->group(function () {
    Route::get('/dashboard',   [CompanyDashboard::class, 'index'])->name('dashboard');
    Route::get('/perfil',      [CompanyDashboard::class, 'profile'])->name('profile');
    Route::put('/perfil',      [CompanyDashboard::class, 'updateProfile'])->name('profile.update');
    Route::get('/postulaciones', [CompanyDashboard::class, 'applications'])->name('applications');
    Route::put('/postulaciones/{application}', [CompanyDashboard::class, 'updateApplicationStatus'])
        ->name('applications.update');

    // CRUD de vacantes
    Route::resource('vacantes', JobPostingController::class, [
        'except' => ['show'],
        'names'  => [
            'index'   => 'jobs.index',
            'create'  => 'jobs.create',
            'store'   => 'jobs.store',
            'edit'    => 'jobs.edit',
            'update'  => 'jobs.update',
            'destroy' => 'jobs.destroy',
        ],
        'parameters' => [
            'vacantes' => 'jobPosting',
        ],
    ]);
});

// ─── Panel de ESTUDIANTE ───────────────────────────────────────────────────────
Route::prefix('estudiante')->name('student.')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard',     [StudentDashboard::class, 'index'])->name('dashboard');
    Route::get('/perfil',        [StudentDashboard::class, 'profile'])->name('profile');
    Route::put('/perfil',        [StudentDashboard::class, 'updateProfile'])->name('profile.update');
    Route::get('/postulaciones', [StudentDashboard::class, 'myApplications'])->name('applications');

    // Vacantes
    Route::get('/vacantes',              [JobController::class, 'index'])->name('jobs');
    Route::get('/vacantes/{jobPosting}', [JobController::class, 'show'])->name('jobs.show');
    Route::post('/vacantes/{jobPosting}/postular', [JobController::class, 'apply'])->name('jobs.apply');
});
