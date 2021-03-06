<?php

namespace App\Http\Controllers;

use App\Http\Utils\StatusCodeUtils;
use App\Repositories\DashboardRepository;
use Exception;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(DashboardRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * Contabiliza os clientes cadastrados
	 */
    public function countClients() 
    {
        try {

			$advocateUserId = Auth::user()->id;
			$data =  $this->repository->countClients($advocateUserId);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
    }

	/**
	 * Contabiliza os contratos
	 */
    public function countContracts() 
    {
        try {

			$advocateUserId = Auth::user()->id;
			$data =  $this->repository->countContracts($advocateUserId);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
    }

	public function countMeetings() 
	{
		try {

			$advocateUserId = Auth::user()->id;
			$data =  $this->repository->countMeetings($advocateUserId);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Obtém os processos
	 */
	public function getProcesses()
	{
		try {

			$advocateUserId = Auth::user()->id;
			$data =  $this->repository->getProcessesByStatus($advocateUserId);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Obtém os processos
	 */
	public function getContracts()
	{
		try {

			$advocateUserId = Auth::user()->id;
			$data =  $this->repository->getContracts($advocateUserId);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Obtém os clientes pelo ano
	 */
	public function getClients()
	{
		try {

			$advocateUserId = Auth::user()->id;
			$data =  $this->repository->getClients($advocateUserId);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Obtém o lucro anual
	 */
	public function getAnnualProfit()
	{
		try {

			$advocateUserId = Auth::user()->id;
			$data =  $this->repository->getAnnualProfit($advocateUserId);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Obtém as reuniões agendadas na semana
	 */
	public function getMeetingsForWeek() 
	{
		try {

			$advocateUserId = Auth::user()->id;
			$data =  $this->repository->getMeetingsForWeek($advocateUserId);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Obtém o processo do cliente
	 */
	public function getProcessByClient()
	{
		try {

			$userId = Auth::user()->id;
			$data =  $this->repository->getProcessByClient($userId);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	
	/**
	 * Obtém o contrato do cliente
	 */
	public function getContractByClient()
	{
		try {

			$userId = Auth::user()->id;
			$data =  $this->repository->getContractByClient($userId);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Obtém a reunião agendada do cliente
	 */
	public function getMeetingByClient()
	{
		try {

			$userId = Auth::user()->id;
			$data =  $this->repository->getMeetingByClient($userId);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}
}
