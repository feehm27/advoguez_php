<?php

namespace App\Http\Controllers;

//Repositories

use App\Http\Requests\Message\Index;
use App\Http\Requests\Message\Store;

//Utils
use App\Http\Utils\StatusCodeUtils;

//Repository
use App\Repositories\MessageRepository;

//Excepction
use Exception;

class MessageController extends Controller
{
    public function __construct(MessageRepository $repository)
	{
		$this->repository = $repository;
	}

    public function index(Index $request)
    {
        try {

			$advocateUserId = $request->advocate_user_id;
            $clientId = $request->client_id;

			$data = $this->repository->getMessages($advocateUserId, $clientId);

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
                'sender_name'       => $request->sender_name,
                'recipient_name'    => $request->recipient_name,
                'subject'           => $request->subject,
                'message'           => $request->message,
                'read'              => $request->read,
                'client_send'       => $request->client_send,
                'advocate_send'     => $request->advocate_send,
                'user_id'           => $request->user_id,
            ];

			$data = $this->repository->store($inputs);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);

		} catch (Exception $error) 
        {
			return response()->json(['error' => $error->getMessage()], 
                StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	public function getMessagesSent(Index $request)
    {
        try {

		    $userId = $request->user_id;
			$clientSent = $request->client_sent;
        
			$data = $this->repository->getMessagesSent($userId, $clientSent);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
    }
}
