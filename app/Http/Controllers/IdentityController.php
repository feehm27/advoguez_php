<?php

namespace App\Http\Controllers;

use App\Http\Requests\Identity\Upload;
use App\Http\Utils\StatusCodeUtils;
use Exception;

class IdentityController extends Controller
{
	public function __construct(IdentityRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * Faz upload da logomarca do advogado
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
