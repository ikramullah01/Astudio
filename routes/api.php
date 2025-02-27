<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\AttributeController;
use App\Http\Controllers\API\TimesheetController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post("register",[AuthController::class,"register"]);
Route::post("login",[AuthController::class,"login"]);
Route::put("user/{id}",[AuthController::class,"updateUser"]);
Route::delete('user/{id}', [AuthController::class, 'deleteUser']);

Route::middleware('auth:api')->group(function () {
    // Routes that require the user to be authenticated
    Route::apiResource('projects', ProjectController::class);
    Route::put('/projects/{projectId}/attributes', [ProjectController::class, 'updateAttributes']);
    Route::post('/projects/{projectId}/assign-user/{userId}', [ProjectController::class, 'assignUserToProject']); // Assign user to project
    Route::get('/projects', [ProjectController::class, 'getProjectsWithAttributes']);

    Route::post('/attributes', [AttributeController::class, 'store']);

    Route::apiResource('timesheets', TimesheetController::class);
});
