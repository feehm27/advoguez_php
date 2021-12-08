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
     * @OA\Get(
     *     tags={"Advocate"},
     *     summary="Retorna os dados do advogado",
     *     description="Retorna os dados do advogado",
     *     path="/advocates/informations",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Dados do advogado."),
	 *      @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Identificador do usuário",
     *         required=false,
	 * 		   @OA\Schema(
     *           type="integer",
	 * 			 format="int64"
     *         )
     *      ),
     * ),
     * 
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
     * @OA\Post(
     *     tags={"Advocate"},
     *     summary="Cria ou atualiza os dados do advogado",
     *     description="Cria ou atualiza os dados do advogado",
     *     path="/advocates/informations",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Advogado criado ou atualizado."),
	 *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer"),
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="cpf", type="string"),
     *              @OA\Property(property="nationality", type="string"),
     *              @OA\Property(property="civil_status", type="string"),
     *              @OA\Property(property="register_oab", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="cep", type="string"),
     *              @OA\Property(property="street", type="string"),
     *              @OA\Property(property="number", type="integer"),
     *              @OA\Property(property="complement", type="string"),
     *              @OA\Property(property="district", type="string"),
     *              @OA\Property(property="state", type="string"),
     *              @OA\Property(property="city", type="string"),
     *              @OA\Property(property="agency", type="string"),
     *              @OA\Property(property="account", type="string"),
     *              @OA\Property(property="bank", type="string"),
     *          )
     *      ),
     * ),
     * 
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
