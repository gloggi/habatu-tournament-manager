<?php

use App\Http\Controllers\ActionsController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\TimeslotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TeamController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\UserController;

Route::apiResource('teams', TeamController::class);
Route::apiResource('halls', HallController::class);
Route::apiResource('sections', SectionController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('timeslots', TimeslotController::class);
Route::apiResource('games', GameController::class);
Route::apiResource('users', UserController::class);
Route::post('options', [OptionController::class, 'store']);
Route::get('options', [OptionController::class, 'index']);
Route::post('tournament/calculate', [TournamentController::class, 'calculateTournamentInfos']);
Route::get('tournament/specs', [TournamentController::class, 'getSpecs']);
Route::get('tournament/table', [TournamentController::class, 'getNormalTable']);
Route::post('tournament/create', [TournamentController::class, 'createTorunament']);
Route::get('tournament/ranking', [TournamentController::class, 'getRanking']);
Route::get('tournament/referee-table', [TournamentController::class, 'getRefereeTable']);
Route::get('tournament/team-table', [TournamentController::class, 'getTeamTable']);
/* 

Route::get('tournament/test', [TournamentController::class, 'testScheduleAllGames']);
Route::get('tournament/create-finale-games', [TournamentController::class, 'createAllFinaleGames']); */
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::get('roles', [UserController::class, 'getRoles']);
Route::post('actions/assign-referees', [ActionsController::class, 'assignReferees']);
Route::post('actions/clear-referees', [ActionsController::class, 'clearReferees']);

