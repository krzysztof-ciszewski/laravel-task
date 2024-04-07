<?php

use App\Application\Http\Controllers\GetActivitiesController;
use App\Application\Http\Controllers\ParseReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/activity', GetActivitiesController::class);
Route::post('/report', ParseReportController::class);
