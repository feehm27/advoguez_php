<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\ChangePassword;
use App\Http\Requests\User\Destroy;
use App\Http\Requests\User\Index;
use App\Http\Requests\User\LockOrUnlock;
use App\Http\Requests\User\Update;

use App\Http\Utils\StatusCodeUtils;
use App\Models\User;
use App\Repositories\UserRepository;

use Exception;

use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct(UserRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
     * @OA\Get(
     *     tags={"User"},
     *     summary="Obtém a lista de usuários",
     *     description="Obtém a lista de usuários",
     *     path="/advocates/users",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Lista de usuários."),
     * ),
     * 
    */
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

	/**
     * @OA\Put(
     *     tags={"User"},
     *     summary="Atualiza um usuário",
     *     description="Atualiza um usuário",
     *     path="/advocates/users/{id}",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Usuário atualizado com sucesso"),
	 *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identificador do usuário",
     *         required=true,
	 *         @OA\Schema(
     *           type="integer",
	 * 			)
     *      ),
	 *       @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Nome do usuário",
     *         required=true,
	 *         @OA\Schema(
     *           type="string",
	 * 			)
	 * 		),
	 *      @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Email do usuário",
     *         required=true,
	 *         @OA\Schema(
     *           type="email",
	 * 			)
	 * 		),
	 *      @OA\Parameter(
     *         name="is_client",
     *         in="query",
     *         description="Se o usuário é do tipo cliente",
     *         required=true,
	 *         @OA\Schema(
     *           type="boolean",
	 * 			)
	 * 		),
	 *      @OA\Parameter(
     *         name="is_advocate",
     *         in="query",
     *         description="Se o usuário é do tipo advogado",
     *         required=true,
	 *         @OA\Schema(
     *           type="boolean",
	 * 			)
	 * 		),
	 *      @OA\Parameter(
     *         name="client_id",
     *         in="query",
     *         description="Identificador do cliente",
     *         required=false,
	 *         @OA\Schema(
     *           type="integer",
	 * 			)
	 * 		),
     * ),
     * 
    */
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

			if($request->client_id) {
				$inputs['client_id'] = $request->client_id;
			}

			$data = $this->repository->update($inputs);

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
     *     tags={"User"},
     *     summary="Bloqueia ou desbloqueia um usuário",
     *     description="Bloqueia ou desbloqueia um usuário",
     *     path="/advocates/users/block",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Usuário bloqueado ou desbloqueado com sucesso."),
	 *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Identificador do usuário",
     *         required=true,
	 *         @OA\Schema(
     *           type="integer",
	 * 			)
     *      ),
	 *     @OA\Parameter(
     *         name="blocked",
     *         in="query",
     *         description="Bloquear",
     *         required=true,
	 *         @OA\Schema(
     *           type="boolean",
	 * 			)
     *      ),
     * ),
     * 
    */
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

	/**
     * @OA\Put(
     *     tags={"User"},
     *     summary="Altera a senha do usuário",
     *     description="Altera a senha do usuário",
     *     path="/user/change/password",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Senha alterada com sucesso."),
	 *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Nova senha",
     *         required=true,
	 *         @OA\Schema(
     *           type="password",
	 * 			)
     *      ),
     * ),
     * 
    */
	public function changePassword(ChangePassword $request)
	{
		try{

			$user = $request->user;
			$token = $request->bearerToken();

			$updatePassword = DB::table('password_resets')
				->where(['email' => $user->email, 'token' => $token])
				->first();

			if(!$updatePassword) {
				return response()->json([
					'status_code' 	=>  StatusCodeUtils::BAD_REQUEST,
					'data' 			=>  "Token inválido. Não é possivel recuperar a senha."
				]);
			}
	
			User::where('email', $user->email)->update(['password' => $request->password]);
	
			DB::table('password_resets')->where(['email'=> $user->email])->delete();

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  []
			]);

		}catch(Exception $error) 
		{
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}
}
