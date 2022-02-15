<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessHistory\Destroy;
use App\Http\Requests\ProcessHistory\Index;
use App\Http\Requests\ProcessHistory\Store;

//Utils
use App\Http\Utils\StatusCodeUtils;

//Repositories
use App\Repositories\ProcessHistoricRepository;


use Exception;

class ProcessHistoricController extends Controller
{
    public function __construct(ProcessHistoricRepository $repository)
	{
		$this->repository = $repository;
	}

    /**
     * @OA\Get(
     *     tags={"ProcessHistoric"},
     *     summary="Obtém a lista de historico de um processo",
     *     description="Obtém a lista de historico de um proceso",
     *     path="/advocates/processes/history",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Lista de historicos de um processo."),
     * ),
     * 
    */
    public function index(Index $request)
    {
        try {

			$processId = $request->process_id;
			$data = $this->repository->getProcessesHistory($processId);

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
     *     tags={"ProcessHistoric"},
     *     summary="Cadastra um novo historico do processo",
     *     description="Cadastra um novo historico do processo",
     *     path="/advocates/processes/history",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Historico criado."),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="modification_date", type="string", example="2022-01-03"),
     *              @OA\Property(property="status_process", type="date", example="Petição Inicial"),
     *              @OA\Property(property="modification_description", type="string", example="Alteração do status do processo."),
     *              @OA\Property(property="process_id", type="integer", example="1"),
     *          )
     *     	),
     * ),
     * 
    */
	public function store(Store $request)
	{
		try {

			$inputs = [     
                'modification_date'             => $request->modification_date,
                'status_process'                => $request->status_process,
                'modification_description'      => $request->modification_description,
                'process_id'                    => $request->process_id,
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
     * @OA\Delete(
     *     tags={"ProcessHistoric"},
     *     summary="Deleta o histórico de um processo",
     *     description="Deleta o histórico de um processo",
     *     path="/advocates/processes/history/{id}",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Deleta o historico de um processo."),
	 *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identificador do historico do processo",
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

			$processHistory = $request->process_history;
			$processHistory->delete();

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  []
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}
}
