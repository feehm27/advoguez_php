<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageReceived\Index;
use App\Http\Requests\MessageReceived\Store;
use App\Http\Utils\StatusCodeUtils;
use App\Repositories\MessageReceivedRepository;
use Exception;


class MessageReceivedController extends Controller
{
    public function __construct(MessageReceivedRepository $repository)
	{
		$this->repository = $repository;
	}

    public function index(Index $request)
	{
		try {

            $advocateUserId = $request->user_id;
			$data = $this->repository->getMessages($advocateUserId);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);

		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	public function store(Store $request)
	{
		try {

			$inputs = [
                'subject'          => $request->subject,
                'message'          => $request->message,
                'client_id'        => $request->client_id,
                'advocate_user_id' => $request->advocate_user_id
            ];

			$data = $this->repository->store($inputs);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);

		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}
}
