<?php

namespace App\Http\Requests\MenuPermission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

//Utils
use App\Http\Utils\StatusCodeUtils;
use Illuminate\Support\Facades\Auth;

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
			'menus_permissions' => 'required|array',
			'user'              => 'required'
		];
	}

	/**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
		$menusPermissions = [];
		
		if($this->menus_permissions){
			$menusPermissions = json_decode($this->menus_permissions, TRUE);	
		}

		$user = Auth::user();

		$this->merge([
			'menus_permissions' => $menusPermissions,
			'user'              => $user
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
