<?php

namespace App\Http\Controllers;

//Requests
use App\Http\Requests\Advocate\Get;
use App\Http\Requests\Advocate\StoreOrUpdate;

//Utilss
use App\Http\Utils\StatusCodeUtils;

//Repository
use App\Repositories\AdvocateRepository;

//Exception
use Exception;

class AdvocateController extends Controller
{
	public function __construct(AdvocateRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * Obtém os dados do advogado
	 */
	public function get(Get $request)
	{
		try {

			$userId = $request->user->id;
			$data = $this->repository->getInformationsByAdvocate($userId);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Atualiza os menus e suas permissões
	 */
	public function storeOrUpdate(StoreOrUpdate $request)
	{
		try {

			$inputs = [
				'id' 	  		=> $request->id,
				'name'			=> $request->name,
				'cpf'			=> $request->cpf,
				'nationality' 	=> $request->nationality,
				'civil_status'  => $request->civil_status,
				'register_oab'  => $request->register_oab,
				'email' 	    => $request->email,
				'cep'           => $request->cep,
				'street'		=> $request->street,
				'number'		=> $request->number,
				'complement'	=> $request->complement,
				'district'		=> $request->district,
				'state'			=> $request->state,
				'city'			=> $request->city,
				'agency'		=> $request->agency,
				'account'		=> $request->account,
				'bank'			=> $request->bank,
				'user_id'		=> $request->user_id
			];

			$data = $this->repository->storeOrUpdate($inputs);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}
}
