<?php

namespace App\Http\Controllers;

use App\Http\Requests\Identity\Upload;
use App\Http\Utils\StatusCodeUtils;
use App\Repositories\IdentityRepository;

use Exception;

class IdentityController extends Controller
{
	public function __construct(IdentityRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
     * @OA\Post(
     *     tags={"Visual Identity"},
     *     summary="Upload da logomarca",
     *     description="Faz upload da logomarca",
     *     path="/identity",
	 *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Logomarca criada."),
	 *      @OA\Parameter(
     *         name="image",
     *         in="query",
     *         description="Upload de imagem",
     *         required=false,
	 * 			@OA\Schema(
     *           type="file",
     *         )
     *      ),
     * ),
     * 
    */
	public function upload(Upload $request)
	{
		try {

			$userId = $request->user->id;
			$image = $request->file('image');

			$data = $this->repository->upload($image, $userId);

			return response()->json([
				'status' 	=>  StatusCodeUtils::SUCCESS,
				'data' 		=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}
}
