<?php

namespace App\Http\Requests\MenuPermission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

//Utils
use App\Http\Utils\StatusCodeUtils;


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
			'menu_permissions' 			=> 'required|json',
			'menu_permissions_array'	=> 'required|array'
		];
	}

	/**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
		$this->menuPermissionsArray = [];

		if ($this->menu_permissions) {
			$this->menuPermissionsArray = json_decode($this->menu_permissions, true);
		}

		$this->merge([
			'menu_permissions_array'	=>  $this->menuPermissionsArray
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
