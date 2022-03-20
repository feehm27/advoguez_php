<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPassword;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Auth\Login;
use App\Http\Requests\Auth\LoginFacebook;
use App\Http\Requests\Auth\Register;
use App\Http\Utils\StatusCodeUtils;
use App\Mail\ResetPasswordMail;
use App\Models\Client;
use App\Models\ClientUser;

//Model
use App\Models\User;
use App\Repositories\AuthRepository;
use Carbon\Carbon;

//Exception
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
	public function __construct(AuthRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
     * @OA\Post(
     *     tags={"Authentication"},
     *     summary="Cadastra um novo usuário",
     *     description="Cria um novo usuário",
     *     path="/register",
	 *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Usuário criado."),
	 *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="is_client", type="boolean"),
     *              @OA\Property(property="is_advocate", type="boolean"),
     *              @OA\Property(property="facebook_id", type="string"),
     *              @OA\Property(property="password", type="string"),
     *          )
     *      ),
     * ),
     * 
    */
	public function register(Register $request)
	{
		try {

			$inputs = [
				'name' 		  		=> $request['name'],
				'email' 	  		=> $request['email'],
				'is_client'   		=> $request['is_client'],
				'is_advocate' 		=> $request['is_advocate'],
				'facebook_id' 		=> $request['facebook_id'],
				'password' 	  		=> $request['password'],
				'advocate_user_id'	=> $request['advocate_user_id']
			];

			$user = User::create($inputs);
			$token = $user->createToken('auth_token')->plainTextToken;
			$advocateUserId = $request->advocate_user_id;

			if ($user) {
				$this->repository->attachPermissions($user, $advocateUserId);
			}

			if($request->client_id) {
				$inputs = ['user_id' => $user->id, 'client_id' => $request->client_id];
				ClientUser::create($inputs);
			}

			return response()->json([
				'access_token' 	=>  $token,
				'token_type'   	=> 'Bearer',
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	/**
     * @OA\Post(
     *     tags={"Authentication"},
     *     summary="Loga um usuário e retorna o token",
     *     description="Loga um usuário",
     *     path="/login",
     *     @OA\Response(response="200", description="Usuário logado."),
	 *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *          )
     *      ),
     * ),
     * 
    */
	public function login(Login $request)
	{
		try {

			$credentials = $request->only('email', 'password');

			if (!Auth::attempt($credentials)) {
				return response()->json([
					'message' => 'Login ou senha inválidos'
				], StatusCodeUtils::UNAUTHORIZED);
			}

			$user = User::where('email', $request['email'])->firstOrFail();

			if($user->blocked === 1){ 
				return response()->json(['message' => "Usuário bloqueado. Acesso não permitido."],
				 StatusCodeUtils::INTERNAL_SERVER_ERROR);
			}

			$token = $user->createToken('auth_token')->plainTextToken;

			return response()->json([
				'access_token' 	 => $token,
				'token_type'	 => 'Bearer',
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Desloga um usuário pelo token
	 */
	public function logout()
	{
		Auth::logout();
	}

	/**
     * @OA\Get(
     *     tags={"Authentication"},
     *     summary="Obtém os dados do usuário",
     *     description="Obtém os dados do usuário",
     *     path="/me",
	 *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Dados do usuário."),
     * ),
     * 
    */
	public function me(Request $request)
	{
		$user = $request->user();
		$user->checkeds = $this->repository->getPermissionsByUser($user);
		$user->logo = $this->repository->getLogoByUser($user);
		$user->isAdmin = false;

		if($user->is_advocate === 1 && $user->advocate_user_id === null){
			$user->isAdmin = true;
		}

		if($user->is_client === 1) {
			
			$clientUser = ClientUser::where('user_id', $user->id)->first();

			if($clientUser){
				$user->client = Client::find($clientUser->client_id);
			}
		}

		return $user;
	}

	/**
     * @OA\Post(
     *     tags={"Authentication"},
     *     summary="Loga ou cria o usuário integrando com o Facebook",
     *     description="Loga ou cria o usuário integrando com o Facebook",
     *     path="/facebook",
     *     @OA\Response(response="200", description="Usuário logado pelo facebook."),
	 *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="is_client", type="boolean"),
     *              @OA\Property(property="is_advocate", type="boolean"),
     *              @OA\Property(property="facebook_id", type="string"),
     *              @OA\Property(property="password", type="string"),
	 *              @OA\Property(property="advocate_user_id", type="integer"),
     *          )
     *      ),
     * ),
     * 
     * 
    */
	public function loginWithFacebook(LoginFacebook $request)
	{
		try{

			$existUser = User::where('facebook_id', $request->facebook_id)->first();
			
			if($existUser) {

				Auth::loginUsingId($existUser->id);
				$token = $existUser->createToken('auth_token')->plainTextToken;

				return response()->json([
					'access_token' 	 => $token,
					'token_type'	 => 'Bearer',
				]);
			}
			
			$inputs = [
				'name' 		  		=> $request['name'],
				'email' 	  		=> $request['email'],
				'is_client'   		=> $request['is_client'],
				'is_advocate' 		=> $request['is_advocate'],
				'facebook_id' 		=> $request['facebook_id'],
				'password' 	  		=> $request['password'],
				'advocate_user_id'	=> $request['advocate_user_id']
			];
			
			$user = User::create($inputs);
			$token = $user->createToken('auth_token')->plainTextToken;

			if ($user) {
				$this->repository->attachPermissions($user);
			}

			return response()->json([
				'access_token' 	=>  $token,
				'token_type'   	=> 'Bearer',
			]);
	
		}catch(Exception $error){
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	/**
     * @OA\Post(
     *     tags={"Authentication"},
     *     summary="Recuperação de senha",
     *     description="Recupera a senha do usuário",
     *     path="/forgot-password",
     *     @OA\Response(response="200", description="Email de recuperação de senha enviado com sucesso."),
	 *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string"),
     *          )
     *      ),
     * ),
     * 
    */
	public function forgotPassword(ForgotPassword $request)
	{
		try{

			$existUser = User::where('email', $request->email)->first();
	
			if(!$existUser){
				return response()->json([
					'status_code' 	=>  StatusCodeUtils::BAD_REQUEST,
					'errors' 		=>  ["email" => "Não conseguimos encontrar um usuário com esse endereço de e-mail"]
				]);
			}

			$token = $existUser->createToken('auth_token')->plainTextToken;

			DB::table('password_resets')->insert([
				'email' => $request->email, 
				'token' => $token, 
				'created_at' => Carbon::now()
			  ]);

			$existUser->token = $token;

			Mail::to($existUser->email)->send(new ResetPasswordMail($existUser));
			
		}catch(Exception $error) {
			return response()->json(['error' => $error->getMessage()],StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}
}
