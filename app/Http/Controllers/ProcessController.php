<?php

namespace App\Http\Controllers;

use App\Http\Requests\Process\Destroy;
use App\Http\Requests\Process\Index;
use App\Http\Requests\Process\Store;
use App\Http\Requests\Process\Update;
use App\Http\Utils\StatusCodeUtils;
use App\Repositories\ProcessRepository;
use Exception;

class ProcessController extends Controller
{
    public function __construct(ProcessRepository $repository)
	{
		$this->repository = $repository;
	}

    /**
     * @OA\Get(
     *     tags={"Process"},
     *     summary="Obtém a lista de processos do advogado",
     *     description="Obtém a lista de processos do advogado",
     *     path="/advocates/processes",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Lista de processos."),
     * ),
     * 
    */
    public function index(Index $request)
    {
        try {

			$advocateUserId = $request->user->id;
			$data = $this->repository->getProcesses($advocateUserId);

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
     *     tags={"Process"},
     *     summary="Cadastra um novo processo",
     *     description="Cria um novo processo",
     *     path="/advocates/processes",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Processo criado."),
     *     @OA\Parameter(
     *         name="file",
     *         in="query",
     *         description="Identificador do cliente",
     *         required=true,
	 *         @OA\Schema(
     *           type="file",
	 * 			)
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="number", type="string", example="5074276-41.2019.8.13.00152"),
     *              @OA\Property(property="labor_stick", type="string", example="5 vara do trabalho"),
     *              @OA\Property(property="petition", type="string", example="Pensão alimenticia"),
     *              @OA\Property(property="status", type="string", example="Petição inicial"),
     *              @OA\Property(property="start_date", type="string", example="2021-05-21"),
     *              @OA\Property(property="end_date", type="string", example="2021-05-21"),
     *              @OA\Property(property="observations", type="string", example="Observação"),
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
                'number'            => $request->number,
                'labor_stick'       => $request->labor_stick,
                'petition'          => $request->petition,
                'status'            => $request->status,
                'file'              => $request->file,
                'start_date'        => $request->start_date,
                'end_date'          => $request->end_date,
                'observations'      => $request->observations,
                'client_id'         => $request->client_id,
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
     * @OA\Put( 
     *     tags={"Process"},
     *     summary="Atualiza um processo",
     *     description="Atualiza um processo",
     *     path="advocates/processes/{id}",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Processo atualizado."),
	 *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identificador do processo",
     *         required=true,
	 *         @OA\Schema(
     *           type="integer",
	 * 			)
     *      ),
	 *       @OA\Parameter(
     *         name="labor_stick",
     *         in="query",
     *         example="5 vara da familia",
     *         description="Vara do trabalho",
     *         required=false,
	 *         @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
	 *       @OA\Parameter(
     *         name="petition",
     *         in="query",
     *         description="Assunto da petição",
     *         required=false,
	 *     	   @OA\Schema(
     *           type="integer",
	 * 			)
     *      ),
	 *      @OA\Parameter(
     *         name="file",
     *         in="query",
     *         description="Arquivo anexado do processo",
     *         required=true,
	 *          @OA\Schema(
     *           type="file",
	 * 			)
     *      ),
	 *      @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Data de inicio",
     *         required=false,
	 *           @OA\Schema(
     *           type="date",
	 * 			)
     *      ),
	 *      @OA\Parameter(
     *         name="agency",
     *         in="query",
     *         description="Data de encerramento",
     *         required=false,
	 *          @OA\Schema(
     *           type="date",
	 * 			)
     *      ),
	 *      @OA\Parameter(
     *         name="observation",
     *         in="query",
     *         description="Observações",
     *         required=false,
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
                'number'            => $request->number,
                'labor_stick'       => $request->labor_stick,
                'petition'          => $request->petition,
                'status'            => $request->status,
                'start_date'        => $request->start_date,
                'end_date'          => $request->end_date,
                'observations'      => $request->observations,
			];

            if($request->file){
                $inputs['file'] = $request->file;
            }

            $process = $request->process; 

			$data = $this->repository->update($process, $inputs);

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
     *     tags={"Process"},
     *     summary="Deleta um processo",
     *     description="Deleta um processo",
     *     path="/advocates/processes/{id}",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Deleta um processo."),
	 *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identificador do processo",
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

			$process = $request->process;
			$process->delete();

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  []
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}
}
