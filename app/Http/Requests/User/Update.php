<?php

namespace App\Http\Requests\User;

use App\Http\Utils\StatusCodeUtils;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Update extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
            'id'          		=> 'required|integer',
			'name' 		  		=> 'required|string|max:255',
			'email'       		=> 'required|string|email|max:255|unique:users,email,' . $this->id,
			'is_client'   		=> 'nullable|boolean',
			'is_advocate' 		=> 'nullable|boolean',
			'facebook_id' 		=> 'nullable|string',
			'password' 	  		=> 'required|string|min:8',
			'advocate_user_id'  => 'nullable|integer',
		];
	}

	/**
	 * Get the error messages for thec defined validation rules.
	 *
	 * @return array
	 */
	public function messages()
	{
		return [
			'email.unique' => 'Já existe um usuário cadastrado com este email',
		];
	}

	/**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
		$user = Auth::user();
		$this->advocateUserId = null;

		if($user->is_advocate == 1){
			$this->advocateUserId = $user->id;
		}

		$this->merge([
            'id'       			=> $this->id,
			'password' 			=>  Hash::make($this->password),
			'facebook_id' 		=> null,
			'advocate_user_id'  => $this->advocateUserId
		]);
	}

	/**
	 * Return validation errors as json response
	 *
	 * @param Validator $validator
	 */
	protected function failedValidation(Validator $validator)
	{
		$response = [
			'status_code' => StatusCodeUtils::BAD_REQUEST,
			'errors'      => $validator->errors(),
		];
		throw new HttpResponseException(response()->json($response, StatusCodeUtils::BAD_REQUEST));
	}
}
