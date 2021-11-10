<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


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
		$this->merge([
            'id'       			=> $this->id,
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
