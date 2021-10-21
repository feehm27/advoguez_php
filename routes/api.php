<?php

use App\Http\Controllers\AdvocateController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\IdentityController;
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

	/**
	 * Rotas necessárias para os dados do advogado
	 */
	Route::prefix('advocates/informations')->group(function () {
		Route::get('', [AdvocateController::class, 'get']);
		Route::post('', [AdvocateController::class, 'storeOrUpdate']);
	});

	/**
	 * Rotas necessárias para os dados de identidade do advogado
	 */
	Route::prefix('identity')->group(function () {
		Route::get('logo', [IdentityController::class, 'getLogo']);
		Route::post('upload', [IdentityController::class, 'upload']);
	});

	/**
	 * Rotas necessárias para a gestão de clientes
	 */
	Route::prefix('advocates/clients')->group(function () {
		Route::get('', [ClientController::class, 'index']);
		Route::get('/{id}', [ClientController::class, 'show']);
		Route::post('', [ClientController::class, 'create']);
		Route::put('/{id}', [ClientController::class, 'update']);
		Route::delete('/{id}', [ClientController::class, 'destroy']);
	});
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
