<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contract\Canceled;
use App\Http\Requests\Contract\Destroy;
use App\Http\Requests\Contract\Index;
use App\Http\Requests\Contract\Show;
use App\Http\Requests\Contract\Store;
use App\Http\Requests\Contract\Update;
use App\Http\Utils\StatusCodeUtils;
use App\Repositories\ContractRepository;
use Exception;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    public function __construct(ContractRepository $repository)
	{
		$this->repository = $repository;
	}

    /**
     * @OA\Get(
     *     tags={"Contract"},
     *     summary="Obtém a lista de contratos do advogado",
     *     description="Obtém a lista de contratos do advogado",
     *     path="/advocates/contracts",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Lista de contratos."),
     * ),
     * 
    */
    public function index(Index $request)
    {
        try {

			$advocateUserId = $request->user->id;
			$data = $this->repository->getContracts($advocateUserId);

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
     *     tags={"Contract"},
     *     summary="Cadastra um novo contrato",
     *     description="Cria um novo contrato",
     *     path="/advocates/contract",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Contrato criado."),
     *  *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="start_date", type="string", example="2021-05-21"),
     *              @OA\Property(property="finish_date", type="string", example="2021-05-21"),
     *              @OA\Property(property="payment_day", type="integer", example="30"),
     *              @OA\Property(property="contract_price", type="double", example="150,00"),
     *              @OA\Property(property="fine_price", type="double", example="100,00"),
     *              @OA\Property(property="agency", type="string", example="1963-4"),
     *              @OA\Property(property="account", type="string", example="10555-9"),
     *              @OA\Property(property="bank", type="string", example="Banco do brasil"),
     *              @OA\Property(property="advocate_id", type="integer", example="1"),
     *              @OA\Property(property="client_id", type="integer", example="2"),
     *          )
     *     	),
     * ),
     * 
    */
	public function store(Store $request)
	{
		try {

			$inputs = [     
                'start_date'        => $request->start_date,
                'finish_date'       => $request->finish_date,
                'payment_day'       => $request->payment_day,
                'contract_price'    => $request->contract_price,
                'fine_price'        => $request->fine_price,
                'agency'            => $request->agency,
                'account'           => $request->account,
                'bank'              => $request->bank,
                'client_id'         => $request->client_id,
                'advocate_id'       => $request->advocate_id,
                'advocate_user_id'  => $request->advocate_user_id    
			];

			$data = $this->repository->create($inputs);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	/**
     * @OA\Get(
     *     tags={"Contract"},
     *     summary="Obtém um contrato pelo seu identificador",
     *     description="Obtém um contrato pelo seu identificador",
     *     path="/advocates/contract/{id}",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Contrato."),
	 *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identificador do contrato",
     *         required=true,
	 *         @OA\Schema(
     *           type="integer",
	 * 			)
     *      ),
     * ),
     * 
    */
	public function show(Show $request) {
		try {

			$contract = $request->contract;

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $contract
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	/**
     * @OA\Put(
     *     tags={"Contract"},
     *     summary="Atualiza um contrato",
     *     description="Atualiza um contrato",
     *     path="/advocates/contracts/{id}",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Contrato atualizado."),
	 *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Identificador do contrato",
     *         required=true,
	 *         @OA\Schema(
     *           type="integer",
	 * 			)
     *      ),
	 *       @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Data inicial do contrato",
     *         required=true,
     *         example="2021-12-01",
	 *         @OA\Schema(
     *           type="date",
	 * 			)
     *      ),
	 *       @OA\Parameter(
     *         name="finish_date",
     *         in="query",
     *         example="2021-12-01",
     *         description="Data final do contrato",
     *         required=true,
	 *         @OA\Schema(
     *           type="date",
	 * 			)
     *      ),
	 *       @OA\Parameter(
     *         name="payment_day",
     *         in="query",
     *         description="Dia do pagamento",
     *         required=true,
	 *     	   @OA\Schema(
     *           type="integer",
	 * 			)
     *      ),
	 *      @OA\Parameter(
     *         name="contract_price",
     *         in="query",
     *         description="Preço do contrato",
     *         required=true,
	 *          @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
	 *      @OA\Parameter(
     *         name="fine_price",
     *         in="query",
     *         description="Valor da multa",
     *         required=true,
	 *           @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
	 *      @OA\Parameter(
     *         name="agency",
     *         in="query",
     *         description="Agência para pagamento",
     *         required=true,
	 *          @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
	 *      @OA\Parameter(
     *         name="account",
     *         in="query",
     *         description="Conta para pagamento",
     *         required=true,
	 *          @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
	 *      @OA\Parameter(
     *         name="bank",
     *         in="query",
     *         description="Banco para pagamento",
     *         required=true,
	 *          @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
     * ),
     * 
    */
	public function update(Update $request)
	{
		try {

			$inputs = [     
                'start_date'        => $request->start_date,
                'finish_date'       => $request->finish_date,
                'payment_day'       => $request->payment_day,
                'contract_price'    => $request->contract_price,
                'fine_price'        => $request->fine_price,
                'agency'            => $request->agency,
                'account'           => $request->account,
                'bank'              => $request->bank,
			];

            $contract = $request->contract; 

			$data = $this->repository->update($contract, $inputs);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

    /**
     * @OA\Put(
     *     tags={"Contract"},
     *     summary="Cancela um contrato",
     *     description="Cancela um contrato",
     *     path="/advocates/contracts/canceled",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Contrato cancelado."),
	 *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Identificador do contrato",
     *         required=true,
	 *         @OA\Schema(
     *           type="integer",
	 * 			)
     *      ),
	 *       @OA\Parameter(
     *         name="canceled",
     *         in="query",
     *         description="Data de cancelamento do contrato",
     *         required=true,
     *         example="2021-12-01",
	 *         @OA\Schema(
     *           type="date",
	 * 			)
     *      ),
     *    ),
     * ),
     * 
    */
    public function canceled(Canceled $request)
    {   
        try {

            $contract = $request->contract;
            $contract->canceled_at = $request->canceled_at;
            $contract->save();

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $contract,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
    }

    /**
     * @OA\Delete(
     *     tags={"Contract"},
     *     summary="Deleta um contrato",
     *     description="Deleta um contrato",
     *     path="/advocates/contracts/{id}",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Deleta um contrato."),
	 *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identificador do contrato",
     *         required=true,
	 *         @OA\Schema(
     *           type="integer",
	 * 			)
     *      ),
     * ),
     * 
    */
	public function destroy(Destroy $request)
	{
		try {

			$contract = $request->contract;
			$contract->delete();

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  []
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
}
