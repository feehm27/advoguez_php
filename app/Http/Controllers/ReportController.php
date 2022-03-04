<?php

namespace App\Http\Controllers;

use App\Http\Requests\Report\Destroy;
use App\Http\Requests\Report\Index;
use App\Http\Requests\Report\Store;
use App\Http\Requests\Report\StoreClient;
use App\Http\Requests\Report\StoreContract;
use App\Http\Requests\Report\StoreProcess;
use App\Http\Requests\Report\Update;
use App\Http\Utils\StatusCodeUtils;
use App\Repositories\ReportRepository;
use Exception;

class ReportController extends Controller
{
    public function __construct(ReportRepository $repository)
	{
		$this->repository = $repository;
	}

    /**
     * @OA\Get(
     *     tags={"Report"},
     *     summary="Obtém a lista de relatórios do advogado",
     *     description="Obtém a lista de relatórios do advogado",
     *     path="/advocates/reports",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Lista de relatórios."),
     * ),
     * 
    */
    public function index(Index $request)
    {
        try {

			$advocateUserId = $request->user->id;
			$data = $this->repository->getReports($advocateUserId);

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
     *     tags={"Report"},
     *     summary="Cria um relatório",
     *     description="Cria um relatório",
     *     path="/advocates/reports",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Relatório criado."),
     *  *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Relatório XPTO"),
     *              @OA\Property(property="type", type="string", example="Clientes"),
     *              @OA\Property(property="export_format", type="string", example="Paisagem"),
     *          )
     *     	),
     * ),
     * 
    */
    public function store(Store $request)
    {
        try {

            $inputs = [     
                'name'                => $request->name,
                'type'                => $request->type,
                'export_format'       => $request->export_format,
                'advocate_user_id'    => $request->advocate_user_id    
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
     *     tags={"Report"},
     *     summary="Atualiza um relatório",
     *     description="Atualiza um relatório",
     *     path="/advocates/reports/{id}",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Relatório atualizado."),
	 *      @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Nome do relatório",
     *         required=true,
	 *         @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
	 *       @OA\Parameter(
     *         name="export_format",
     *         in="query",
     *         description="Formato do relatório",
     *         required=true,
	 *         @OA\Schema(
     *           type="email",
	 * 			)
     *      ),
	 *       @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Tipo do relatório",
     *         required=true,
	 *         @OA\Schema(
     *           type="true",
	 * 			)
     *      ),
     * )
     * 
    */
	public function update(Update $request)
	{
		try {

			$inputs = [         
		        'name'         		=> $request->name,
		        'export_format'     => $request->export_format,
		        'type'              => $request->type,
			];
            
            $report = $request->report;
            $data = $report->update($inputs);

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
     *     tags={"Report"},
     *     summary="Cria um relatório de cliente",
     *     description="Cria um relatório de cliente",
     *     path="/advocates/reports/clients",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Relatório do cliente criado."),
     *  *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="birthday", type="string", example="2021-05-21"),
     *              @OA\Property(property="registration_date", type="string", example="2021-05-21"),
     *              @OA\Property(property="gender", type="string", example="Feminino"),
     *              @OA\Property(property="civil_status", type="string", example="Solteiro"),
     *              @OA\Property(property="report_id", type="integer", example="30"),
     *          )
     *     	),
     * ),
     * 
    */
    public function createClient(StoreClient $request)
    {
        try {

            $report = $request->report;
            $clientReport = $request->client_report;
            $advocateUserId = $request->user->id;
          
            $inputsClient = [
                'birthday'              => $request->birthday,
                'registration_date'     => $request->registration_date,
                'gender'                => $request->gender,
                'civil_status'          => $request->civil_status,
                'report_id'             => $report->id
            ];

            $data = $this->repository->createAndExport($report->type, $inputsClient, $report, $advocateUserId, $clientReport);

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
     *     tags={"Report"},
     *     summary="Cria um relatório de contrato",
     *     description="Cria um relatório de contrato",
     *     path="/advocates/reports/contracts",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Contrato criado."),
     *  *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="start_date", type="string", example="2021-05-21"),
     *              @OA\Property(property="finish_date", type="string", example="2021-05-21"),
     *              @OA\Property(property="canceled_at", type="integer", example="30"),
     *              @OA\Property(property="status", type="string", example="Ativo"),
     *              @OA\Property(property="payment_day", type="integer", example="5"),
     *          )
     *     	),
     * ),
     * 
    */
    public function createContract(StoreContract $request)
    {
        try {

            $report = $request->report;
            $contractReport = $request->contract_report;
            $advocateUserId = $request->user->id;
          
            $inputs = [
                'start_date'              => $request->start_date,
                'finish_date'             => $request->finish_date,
                'canceled_at'             => $request->canceled_at,
                'status'                  => $request->status,
                'payment_day'             => $request->payment_day,
                'report_id'               => $report->id
            ];

            $data = $this->repository->createAndExport($report->type, $inputs, $report, $advocateUserId, $contractReport);

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
     *     tags={"Report"},
     *     summary="Cria um relatório de processo",
     *     description="Cria um relatório de processo",
     *     path="/advocates/reports/processes",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Relatório do processo criado."),
     *  *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="start_date", type="string", example="2021-05-21"),
     *              @OA\Property(property="end_date", type="string", example="2021-05-21"),
     *              @OA\Property(property="stage", type="string", example="Petição inicial"),
     *          )
     *     	),
     * ),
     * 
    */
    public function createProcess(StoreProcess $request)
    {
        try {

            $report = $request->report;
            $processReport = $request->process_report;
            $advocateUserId = $request->user->id;

            $inputs = [
                'start_date'          => $request->start_date,
                'end_date'            => $request->end_date,
                'status'              => $request->status,
                'report_id'           => $report->id
            ];

            $data = $this->repository->createAndExport($report->type, $inputs, $report,  $advocateUserId, $processReport);

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
     *     tags={"Report"},
     *     summary="Deleta um relatório",
     *     description="Deleta um relatório",
     *     path="/advocates/reports/{id}",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Deleta um relatório."),
	 *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identificador do relátorio",
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
            
            $this->repository->deleteReportAndJoins($request->report);
			
			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  []
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}
}
