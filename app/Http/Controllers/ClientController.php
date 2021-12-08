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

	/**
     * @OA\Get(
     *     tags={"Client"},
     *     summary="Obtém a lista de clientes do advogado",
     *     description="Obtém a lista de clientes do advogado",
     *     path="/advocates/clients",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Lista de clientes."),
     * ),
     * 
    */
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

	/**
     * @OA\Post(
     *     tags={"Client"},
     *     summary="Cadastra um novo cliente",
     *     description="Cria um novo cliente",
     *     path="/advocates/clients",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Cliente criado."),
     *  *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="cpf", type="string"),
     *              @OA\Property(property="rg", type="string"),
     *              @OA\Property(property="issuing_organ", type="string"),
     *              @OA\Property(property="nationality", type="string"),
     *              @OA\Property(property="birthday", type="date"),
     *              @OA\Property(property="gender", type="string"),
     *              @OA\Property(property="civil_status", type="string"),
     *              @OA\Property(property="telephone", type="string"),
     *              @OA\Property(property="cellphone", type="string"),
     *              @OA\Property(property="cep", type="string"),
     *              @OA\Property(property="street", type="string"),
     *              @OA\Property(property="number", type="integer"),
     *              @OA\Property(property="complement", type="string"),
     *              @OA\Property(property="district", type="string"),
     *              @OA\Property(property="city", type="string"),
     *              @OA\Property(property="name_user", type="string"),
     *              @OA\Property(property="email_user", type="string"),
     *              @OA\Property(property="password_user", type="string"),
     *          )
     *     	),
     * ),
     * 
    */
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

	/**
     * @OA\Get(
     *     tags={"Client"},
     *     summary="Obtém um cliente pelo seu identificador.",
     *     description="Obtém um cliente pelo seu identificador",
     *     path="/advocates/clients/{id}",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Lista de clientes."),
	 *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identificador do cliente",
     *         required=true,
	 *         @OA\Schema(
     *           type="integer",
	 * 			)
     *      ),
     * ),
     * 
    */
	public function show(Show $request) {
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

	/**
     * @OA\Put(
     *     tags={"Client"},
     *     summary="Atualiza um cliente",
     *     description="Atualiza um cliente",
     *     path="/advocates/clients",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Cliente atualizado."),
	 *      @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Nome do cliente",
     *         required=true,
	 *         @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
	 *       @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="E-mail do cliente",
     *         required=true,
	 *         @OA\Schema(
     *           type="email",
	 * 			)
     *      ),
	 *       @OA\Parameter(
     *         name="cpf",
     *         in="query",
     *         description="CPF do cliente",
     *         required=true,
	 *         @OA\Schema(
     *           type="true",
	 * 			)
     *      ),
	 *       @OA\Parameter(
     *         name="rg",
     *         in="query",
     *         description="RG do cliente",
     *         required=true,
	 *     	   @OA\Schema(
     *           type="true",
	 * 			)
     *      ),
	 *      @OA\Parameter(
     *         name="issuing_organ",
     *         in="query",
     *         description="Orgão emissor",
     *         required=true,
	 *          @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
	 *      @OA\Parameter(
     *         name="nationality",
     *         in="query",
     *         description="Nacionalidade do cliente",
     *         required=true,
	 *           @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
	 *      @OA\Parameter(
     *         name="birthday",
     *         in="query",
     *         description="Data de nascimento no formato YYYY-MM-DD",
     *         required=true,
	 *          @OA\Schema(
     *           type="date",
	 * 			)
     *      ),
	 *      @OA\Parameter(
     *         name="gender",
     *         in="query",
     *         description="Gênero",
     *         required=true,
	 *          @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
	 *      @OA\Parameter(
     *         name="civil_status",
     *         in="query",
     *         description="Estado civil",
     *         required=true,
	 *          @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
	 *     @OA\Parameter(
     *         name="telephone",
     *         in="query",
     *         description="Telefone",
     *         required=false,
	 *          @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
	 *     @OA\Parameter(
     *         name="cellphone",
     *         in="query",
     *         description="Celular",
     *         required=true,
	 *          @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
	*      @OA\Parameter(
     *         name="cep",
     *         in="query",
     *         description="CEP do endereço",
     *         required=true,
	 *    	   @OA\Schema(
     *           type="string",
	 * 		   )
     *      ),
	 *     @OA\Parameter(
     *         name="street",
     *         in="query",
     *         description="Rua do endereço",
     *         required=true,
	 *    	   @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
	 *     @OA\Parameter(
     *         name="number",
     *         in="query",
     *         description="Número do endereço",
     *         required=true,
	 *    	   @OA\Schema(
     *           type="integer",
	 *  	     format="int64"
	 * 			)
     *      ),
	 *     @OA\Parameter(
     *         name="complement",
     *         in="query",
     *         description="Complemento do endereço",
     *         required=false,
	 *   	   @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
	 *     @OA\Parameter(
     *         name="district",
     *         in="query",
     *         description="Bairro do endereço",
     *         required=true,
	 *    	    @OA\Schema(
     *           type="string",
	 * 			)
     *      ),
	 *     @OA\Parameter(
     *         name="state",
     *         in="query",
     *         description="Estado do endereço",
     *         required=true,
	 *         @OA\Schema(
     *           type="string",
	 * 		   )
     *      ),
	 *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="Cidade do endereço",
     *         required=true,
	 *  	   @OA\Schema(
     *           type="string",
	 * 			)
     *     	),
     * ),
     * 
    */
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

	/**
     * @OA\Delete(
     *     tags={"Client"},
     *     summary="Deleta um cliente",
     *     description="Deleta um cliente",
     *     path="/advocates/clients/{id}",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Deleta um cliente."),
	 *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identificador do cliente",
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

	/**
     * @OA\Post(
     *     tags={"Client"},
     *     summary="Exporta um ou mais clientes",
     *     description="Exporta um ou mais clientes em pdf.",
     *     path="/advocates/clients/download",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Exporta um ou mais clientes em pdf"),
	  *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer"),
     *              @OA\Property(property="all_clients", type="boolean"),
     *          )
     *      ),
     * )
     * 
    */
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

