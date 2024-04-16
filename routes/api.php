<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\JobController;
use App\Http\Controllers\Api\V1\CandidatureController;

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

//Auth API
Route::post('ping', [UserController::class, 'ping']);
Route::post('login', [UserController::class, 'login']);
Route::get('roles', [UserController::class, 'roles']);
Route::post('register', [UserController::class, 'register']);
Route::post('verify', [UserController::class, 'verify']);
Route::post('ask_mail_reset', [UserController::class, 'ask_mail_reset']);
Route::post('reset_password', [UserController::class, 'reset_password']);
Route::get('jobs', [JobController::class, 'jobs']);
Route::post('get_enterprise', [JobController::class, 'get_enterprise']);
Route::get('companies', [UserController::class, 'companies']);
Route::post('get_job_with_options', [JobController::class, 'get_job_with_options']);


Route::middleware('auth:sanctum')->group( function () {
    //return $request->user();
    Route::post('update_photo', [UserController::class, 'update_photo']);
    Route::post('get_profilestudent', [UserController::class, 'get_profilestudent']);
    Route::post('get_profileenterprise', [UserController::class, 'get_profileenterprise']);
    Route::post('update_profile', [UserController::class, 'update_profile']);
    Route::post('update_password', [UserController::class, 'update_password']);
    Route::post('getKills', [UserController::class, 'getKills']);
    Route::post('addkill', [UserController::class, 'addkill']);
    Route::post('delete_kill', [UserController::class, 'delete_kill']);
    
    Route::get('job_options', [JobController::class, 'job_options']);
   
    Route::post('add_job', [JobController::class, 'add_job']);
    Route::post('getjobs', [JobController::class, 'getjobs']);
    Route::post('candidatures', [JobController::class, 'candidatures']);
    Route::post('own_jobs', [JobController::class, 'own_jobs']);
    Route::post('bookmarks', [JobController::class, 'bookmarks']);
    Route::post('delete_job', [JobController::class, 'delete_job']);
    Route::post('delete_candidature', [JobController::class, 'delete_candidature']);
    Route::post('delete_bookmark', [JobController::class, 'delete_bookmark']);
    Route::post('add_bookmark', [JobController::class, 'add_bookmark']);
    Route::post('apply', [JobController::class, 'apply']);
    Route::post('accept', [JobController::class, 'accept']);
    Route::post('deny', [JobController::class, 'deny']);
    Route::post('my_students', [JobController::class, 'my_students']);
    Route::post('delete_bookmark', [JobController::class, 'delete_bookmark']);
    
});
