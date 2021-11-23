<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPassword;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Auth\Login;
use App\Http\Requests\Auth\LoginFacebook;
use App\Http\Requests\Auth\Register;
use App\Http\Utils\StatusCodeUtils;
use App\Mail\ResetPasswordMail;
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
	 * Registra um novo usuário
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

			if ($user) {
				$this->repository->attachPermissions($user);
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
	 * Autentica um usuário 
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
	 * Obtém um usuário pelo token
	 */
	public function me(Request $request)
	{
		$user = $request->user();
		$user->checkeds = $this->repository->getPermissionsByUser($user);
		$user->logo = $this->repository->getLogoByUser($user->id);

		return $user;
	}

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
