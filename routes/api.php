<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuPermissionController;

use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:sanctum'])->group(function () {

	Route::get('me', [AuthController::class, 'me']);
	Route::post('logout', [AuthController::class, 'logout']);

	/**
	 * Rotas necessárias para as permissões do menu
	 */
	Route::prefix('menu/permissions')->group(function () {
		Route::get('', [MenuPermissionController::class, 'get']);
		Route::post('', [MenuPermissionController::class, 'update']);
	});
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
