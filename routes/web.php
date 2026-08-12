<?php

use App\Http\Controllers\AccueilController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MesureController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\ParticipanteController;
use App\Http\Controllers\ParticipantMediaController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\RecuController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ----- Authentification -----
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ----- Médias publics (hors auth pour les assets) -----
Route::get('/media/public/{path}', [MediaController::class, 'show'])
    ->where('path', '.*')
    ->name('media.public');

// ----- Routes protégées -----
Route::middleware(['auth'])->group(function () {

    // Tableau de bord
    Route::get('/', [AccueilController::class, 'index'])->name('accueil');

    // Statistiques
    Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques.index');

    // Fitness
    Route::get('/participantes/{participante}/photo', [ParticipanteController::class, 'photo'])->name('participantes.photo');
    Route::resource('participantes', ParticipanteController::class);
    Route::patch('/challenges/{challenge}/status', [ChallengeController::class, 'changeStatus'])->name('challenges.status');
    Route::resource('challenges', ChallengeController::class);
    Route::resource('presences', PresenceController::class)->except(['destroy']);
    Route::resource('mesures', MesureController::class);
    Route::get('/participant-media', [ParticipantMediaController::class, 'index'])->name('participant-media.index');
    Route::post('/challenges/{challenge}/media', [ParticipantMediaController::class, 'store'])->name('challenges.media.store');
    Route::get('/participant-media/{media}', [ParticipantMediaController::class, 'show'])->name('participant-media.show');
    Route::delete('/participant-media/{media}', [ParticipantMediaController::class, 'destroy'])->name('participant-media.destroy');
    Route::post('/payments/{paiement}/recu', [RecuController::class, 'store'])->name('payments.recu.store');
    Route::resource('payments', PaiementController::class)->parameters(['payments' => 'paiement']);
    Route::get('/recus', [RecuController::class, 'index'])->name('recus.index');
    Route::get('/recus/{recu}', [RecuController::class, 'show'])->name('recus.show');
    Route::get('/recus/{recu}/pdf', [RecuController::class, 'pdf'])->name('recus.pdf');


    // Administration (réservée aux rôles disposant des permissions de gestion)
    Route::middleware('role:super_admin|manager')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
    });

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});
