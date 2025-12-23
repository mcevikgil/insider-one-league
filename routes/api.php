<?php

use App\Http\Controllers\Api\LeagueController;
use App\Http\Controllers\Api\StandingController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\PredictionController;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteUri;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('teams')->name('teams.')->group(function () {
    Route::get('/', [TeamController::class, 'index'])->name('teams');
    Route::put('/{team}/toggle-select', [TeamController::class, 'toggleSelect'])->name('toggle-select');
});

Route::prefix('league')->name('league.')->group(function () {
    Route::post('/generate-fixtures', [LeagueController::class, 'generateFixtures'])->name('generate-fixtures');
    Route::post('/simulate-week', [LeagueController::class, 'simulateWeek'])->name('simulate-week');
    Route::post('/simulate-all', [LeagueController::class, 'simulateAll'])->name('simulate-all');
    Route::post('/reset', [LeagueController::class, 'reset'])->name('reset');
    Route::get('/current-week', [LeagueController::class, 'currentWeek'])->name('current-week');
    Route::get('/matches', [LeagueController::class, 'matches'])->name('matches');
    Route::get('/matches/week/{week}', [LeagueController::class, 'matches'])->name('matches');
    Route::put('/matches/{game}', [LeagueController::class, 'updateMatch'])->name('update-match');
});

Route::prefix('standings')->name('standings.')->group(function () {
    Route::get('/', [StandingController::class, 'index'])->name('index');
});

Route::prefix('predictions')->name('predictions.')->group(function () {
    Route::get('/', [PredictionController::class, 'index'])->name('index');
});
