<?php

namespace App\Http\Controllers;

use App\Http\Requests\Client\Create;
use App\Http\Requests\Client\Destroy;
use App\Http\Requests\Client\Download;
use App\Http\Requests\Client\Index;
use App\Http\Requests\Client\Show;
use App\Http\Requests\Client\Update;

//Utils
use App\Http\Utils\StatusCodeUtils;

//Repository
use App\Repositories\ClientRepository;

use Exception;

class ClientController extends Controller
{
    public function __construct(ClientRepository $repository)
	{
		$this->repository = $repository;
	}

    public function index(Index $request)
    {
        try {

			$advocateUserId = $request->user->id;
			$data = $this->repository->getClients($advocateUserId);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
    }

	public function create(Create $request)
	{
		try {

			if($request->has_user){
				return [
					'status_code' => StatusCodeUtils::BAD_REQUEST,
					'errors'      => ["email_user" => "Já existe um usuário com este email"]
				];
			}

			$inputsClient = [            
		        'name'         		=> $request->name,
		        'email'        		=> $request->email,
		        'cpf'          		=> $request-> cpf,
		        'rg'           		=> $request->rg,
		        'issuing_organ'		=> $request->issuing_organ,
		        'nationality'  		=> $request->nationality, 
		        'birthday'     		=> $request->birthday,
		        'gender'       		=> $request->gender,
		        'civil_status' 		=> $request->civil_status, 
                'telephone'    		=> $request->telephone,
                'cellphone'    		=> $request->cellphone,
                'cep'          		=> $request->cep,
		        'street'       		=> $request->street,
		        'number'       		=> $request->number,
		        'complement'   		=> $request->complement,
		        'district'     		=> $request->district,
		        'state'        		=> $request->state,
		        'city'              => $request->city,
                'advocate_user_id'  => $request->advocate_user_id
			];

			$inputsUser = [
				'name' 		  		=> $request['name_user'],
				'email' 	  		=> $request['email_user'],
				'is_client'   		=> $request['is_client'],
				'is_advocate' 		=> $request['is_advocate'],
				'facebook_id' 		=> $request['facebook_id'],
				'password' 	  		=> $request['password_user'],
				'advocate_user_id'	=> $request['advocate_user_id']
			];

			$data = $this->repository->createClientAndUser($inputsClient, $inputsUser);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	public function show (Show $request) {
		try {

			$client = $request->client;

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $client
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	public function update(Update $request)
	{
		try {
			$inputs = [     
				'id'                => $request->id,       
		        'name'         		=> $request->name,
		        'email'        		=> $request->email,
		        'cpf'          		=> $request-> cpf,
		        'rg'           		=> $request->rg,
		        'issuing_organ'		=> $request->issuing_organ,
		        'nationality'  		=> $request->nationality, 
		        'birthday'     		=> $request->birthday,
		        'gender'       		=> $request->gender,
		        'civil_status' 		=> $request->civil_status, 
                'telephone'    		=> $request->telephone,
                'cellphone'    		=> $request->cellphone,
                'cep'          		=> $request->cep,
		        'street'       		=> $request->street,
		        'number'       		=> $request->number,
		        'complement'   		=> $request->complement,
		        'district'     		=> $request->district,
		        'state'        		=> $request->state,
		        'city'              => $request->city,
                'advocate_user_id'  => $request->advocate_user_id
			];

			$data = $this->repository->updateClient($inputs);

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

			$client = $request->client;
			$this->repository->delete($client);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  []
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	public function generatePDF(Download $request)
	{
		try {

			$client = $request->client;
			$allClients = $request->all_clients;
			$clients = $request->clients;
			$user = $request->user;
		
			$data = $this->repository->generatePDF($client, $allClients, $clients, $user);

			return [
				"status_code"  => StatusCodeUtils::SUCCESS,
				"link"   => $data
			];

		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}
}

