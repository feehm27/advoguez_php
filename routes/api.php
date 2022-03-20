<?php

use App\Http\Controllers\AdvocateController;
use App\Http\Controllers\AdvocateScheduleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IdentityController;
use App\Http\Controllers\MenuPermissionController;
use App\Http\Controllers\MessageAnswerController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessageReceivedController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ProcessHistoricController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

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

Route::get('/user', function() {return auth()->user();})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {

	/**
	 * Rotas necessárias para o usuário
	 */
	Route::prefix('user')->group(function () {
		Route::get('', function() {return auth()->user();});
		Route::put('change/password', [UserController::class, 'changePassword']);
	});

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
		Route::post('upload', [IdentityController::class, 'upload']);
	});

	/**
	 * Rotas necessárias para a gestão de clientes
	 */
	Route::prefix('advocates/clients')->group(function () {
		Route::get('', [ClientController::class, 'index']);
		Route::get('/{id}', [ClientController::class, 'show']);
		Route::post('/download', [ClientController::class, 'generatePDF']);
		Route::post('', [ClientController::class, 'create']);
		Route::put('/{id}', [ClientController::class, 'update']);
		Route::delete('/{id}', [ClientController::class, 'destroy']);
	});

	/**
	 * Rotas necessárias para a gestão de usuários
	 */
	Route::prefix('advocates/users')->group(function () {
		Route::get('', [UserController::class, 'index']);
		Route::put('/block', [UserController::class, 'lockOrUnlock']);
		Route::put('/{id}', [UserController::class, 'update']);
	});

	/**
	 * Rotas necessárias para a gestão de contratos
	 */
	Route::prefix('advocates/contracts')->group(function () {
		Route::get('', [ContractController::class, 'index']);
		Route::get('/{id}', [ContractController::class, 'show']);
		Route::post('', [ContractController::class, 'store']);
		Route::put('/canceled', [ContractController::class, 'canceled']);
		Route::put('/{id}', [ContractController::class, 'update']);
		Route::delete('/{id}', [ContractController::class, 'destroy']);
	});

	/**
	 * Rotas necessárias para a gestão de histórico dos processos
	 */
	Route::prefix('advocates/processes/historic')->group(function () {
		Route::get('', [ProcessHistoricController::class, 'index']);
		Route::post('', [ProcessHistoricController::class, 'store']);
		Route::delete('/{id}', [ProcessHistoricController::class, 'destroy']);
	});

	/**
	 * Rotas necessárias para a gestão de processos
	 */
	Route::prefix('advocates/processes')->group(function () {
		Route::get('', [ProcessController::class, 'index']);
		Route::post('', [ProcessController::class, 'store']);
		Route::post('/{id}', [ProcessController::class, 'update']);
		Route::delete('/{id}', [ProcessController::class, 'destroy']);
	});

	/**
	 * Rotas necessárias para a gestão da agenda do advogado
	 */
	Route::prefix('advocates/schedules')->group(function () {
		Route::get('', [AdvocateScheduleController::class, 'index']);
		Route::post('', [AdvocateScheduleController::class, 'store']);
		Route::delete('', [AdvocateScheduleController::class, 'destroy']);
	});

	/**
	 * Rotas necessárias para a gestão da agenda do advogado
	 */
	Route::prefix('clients/schedules')->group(function () {
		Route::get('', [ClientController::class, 'getSchedulesForClient']);
		Route::get('check', [ClientController::class, 'checkSchedule']);
		Route::post('', [AdvocateScheduleController::class, 'storeByClient']);
		Route::post('cancel', [ClientController::class, 'cancelMetting']);
	});

	/**
	 * Rotas necessárias para a gestão de relatórios
	 */
	Route::prefix('advocates/reports')->group(function () {
		Route::get('', [ReportController::class, 'index']);
		Route::post('', [ReportController::class, 'store']);
		Route::put('/{id}', [ReportController::class, 'update']);
		Route::delete('/{id}', [ReportController::class, 'destroy']);
		Route::post('clients', [ReportController::class, 'createClient']);
		Route::post('contracts', [ReportController::class, 'createContract']);
		Route::post('processes', [ReportController::class, 'createProcess']);
	});
	
	/**
	 * Rotas necessárias para as mensagens enviadas ao advogado
	 */
	Route::prefix('advocates/messages/received')->group(function () {
		Route::get('', [MessageReceivedController::class, 'index']);
		Route::post('', [MessageReceivedController::class, 'store']);
		Route::post('destroy', [MessageReceivedController::class, 'destroy']);
	});

	/**
	 * Rotas necessárias para as mensagens do cliente
	 */
	Route::prefix('clients/messages/received')->group(function () {
		Route::get('', [MessageReceivedController::class, 'getMessagesByClient']);
	});

	/**
	 * Rotas necessárias para as mensagens respondidas
	 */
	Route::prefix('advocates/messages/answers')->group(function () {
		Route::post('', [MessageAnswerController::class, 'store']);
	});

	/**
	 * Rotas necessárias para a gestão da agenda do advogado
	 */
	Route::prefix('advocates/dashboard')->group(function () {
		Route::get('count/clients', [DashboardController::class, 'countClients']);
		Route::get('count/contracts', [DashboardController::class, 'countContracts']);
		Route::get('count/meetings', [DashboardController::class, 'countMeetings']);
		Route::get('processes', [DashboardController::class, 'getProcesses']);
		Route::get('contracts', [DashboardController::class, 'getContracts']);
		Route::get('clients', [DashboardController::class, 'getClients']);
		Route::get('profit', [DashboardController::class, 'getAnnualProfit']);
		Route::get('meetings', [DashboardController::class, 'getMeetingsForWeek']);
	});

	/**
	 * Rotas necessárias para a gestão da agenda do advogado
	 */
	Route::prefix('clients/dashboard')->group(function () {
		Route::get('process', [DashboardController::class, 'getProcessByClient']);
		Route::get('contract', [DashboardController::class, 'getContractByClient']);
		Route::get('meeting', [DashboardController::class, 'getMeetingByClient']);
	});

	/**
	 * Rotas necessárias para a gestão de contratos do cliente
	 */
	Route::prefix('clients/contracts')->group(function () {
		Route::get('', [ContractController::class, 'getContractByClient']);
	});

	/**
	 * Rotas necessárias para a gestão de processos do cliente
	 */
	Route::prefix('clients/processes')->group(function () {
		Route::get('', [ProcessController::class, 'getProcessByClient']);
	});

});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/facebook', [AuthController::class, 'loginWithFacebook']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

