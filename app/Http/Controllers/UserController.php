<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\Destroy;
use App\Http\Requests\User\Index;
use App\Http\Requests\User\LockOrUnlock;
use App\Http\Requests\User\Update;
use App\Http\Utils\StatusCodeUtils;
use App\Repositories\UserRepository;
use Exception;

class UserController extends Controller
{
    public function __construct(UserRepository $repository)
	{
		$this->repository = $repository;
	}

    public function index(Index $request)
    {
        try {

			$user = $request->user;
			$data = $this->repository->getUsers($user);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
    }

    public function update(Update $request)
    {
        try {

            $inputs = [
                'id'          		=> $request['id'],
                'name' 		  		=> $request['name'],
                'email' 	  		=> $request['email'],
                'is_client'   		=> $request['is_client'],
                'is_advocate' 		=> $request['is_advocate'],
            ];

			$data = $this->repository->update($inputs);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		} 
    }

    public function lockOrUnlock(LockOrUnlock $request)
    {
        try {

			$user = $request->user;
            $input = ["blocked" => $request->blocked];

			$data = $this->repository->lockOrUnlock($user, $input);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
    }

	public function destroy(Destroy $request)
	{
		try {

			$user = $request->user;
			$this->repository->delete($user);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  []
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}
}