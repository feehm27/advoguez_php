<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdvocateSchedule\Index;
use App\Http\Requests\AdvocateSchedule\Store;
use App\Http\Utils\StatusCodeUtils;
use App\Repositories\AdvocateScheduleRepository;
use Exception;

class AdvocateScheduleController extends Controller
{
    public function __construct(AdvocateScheduleRepository $repository)
	{
		$this->repository = $repository;
	}

    /**
     * @OA\Get(
     *     tags={"AdvocateSchedule"},
     *     summary="Obtém os dados da agenda do advogado",
     *     description="Obtém os dados da agenda do advogado",
     *     path="/advocates/schedules",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Agenda."),
     *     @OA\Parameter(
     *         name="date",
     *         in="path",
     *         description="Data a ser pesquisada",
     *         required=true,
	 *         @OA\Schema(
     *           type="date",
	 * 			)
     *      ),
     * ),
     * 
    */
    public function index(Index $request)
    {
        try {

			$advocateUserId = $request->advocate_user_id;
            $date = $request->date;

			$data = $this->repository->getSchedules($advocateUserId, $date);

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
     *     tags={"AdvocateSchedule"},
     *     summary="Cadastra os horários na agenda",
     *     description="Cadastra os horários na agenda",
     *     path="/advocates/schedules",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Agenda atualizada."),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="date", type="date", example="2021-05-21"),
     *              @OA\Property(property="horarys", type="json", example="[{08:30, 09:00}]"),
     *              @OA\Property(property="time_type", type="integer", example=1),
     *          )
     *     	),
     * ),
     * 
    */
	public function store(Store $request)
	{
		try {

			$inputs = [     
                'date'                => $request->date,
                'horarys'             => $request->horarys,
                'time_type'           => $request->time_type,
                'advocate_user_id'    => $request->advocate_user_id,
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
}
