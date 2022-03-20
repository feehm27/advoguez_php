<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageAnswer\Store;
use App\Http\Utils\StatusCodeUtils;
use App\Repositories\MessageAnswerRepository;
use Exception;

class MessageAnswerController extends Controller
{
    public function __construct(MessageAnswerRepository $repository)
	{
		$this->repository = $repository;
	}

    public function store(Store $request)
	{
		try {

			$inputs = [
                'answer'            	=> $request->answer,
                'code_message'      	=> $request->code_message,
				'message_received_id'	=> $request->message_received_id,
				'response_client'       => $request->response_client,
				'response_advocate'		=> $request->response_advocate,
                'advocate_user_id'  	=> $request->advocate_user_id,
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
