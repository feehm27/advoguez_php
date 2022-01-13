<?php

namespace App\Http\Requests\Advocate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

//Utils
use App\Http\Utils\StatusCodeUtils;

class StoreOrUpdate extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		if ($this->user->is_advocate == 1) return true;

		return false;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'id' 	  		=> 'nullable|integer',
			'name'			=> 'required|string|max:200',
			'cpf'			=> 'required|string|max:11|unique:advocates,cpf,' . $this->id,
			'nationality' 	=> 'required|string|max:200',
			'civil_status'  => 'required|string|max:50',
			'register_oab'  => 'required|string|max:8',
			'email' 	    => 'required|string|email|max:255|unique:advocates,email,' . $this->id,
			'cep'           => 'required|string|max:8',
			'street'		=> 'required|string|max:200',
			'number'		=> 'required|integer',
			'complement'	=> 'nullable|string|max:200',
			'district'		=> 'required|string|max:150',
			'state'			=> 'required|string|max:200',
			'city'			=> 'required|string|max:200',
			'agency'		=> 'nullable|string|max:6',
			'account'		=> 'nullable|string|max:30',
			'bank'			=> 'nullable|string|max:100',
			'user_id'		=> 'required|integer',
		];
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages()
	{
		return [
			'email.unique' => "Já existe um cadastro com este email",
			'cpf.unique' => "Já existe um cadastro com este cpf"
		];
	}

	/**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
		$characters = array('.', '/', '-');

		$this->user = Auth::user();

		if ($this->cpf) {
			$this->cpf = str_replace($characters, '', $this->cpf);
		}

		if ($this->cep) {
			$this->cep = str_replace($characters, '', $this->cep);
		}

		$characters = array('.', '/', '-');

		$this->account = str_replace($characters, '', $this->account);
		$this->agency = str_replace($characters, '', $this->agency);

		$this->merge([
			'user_id' 	=> $this->user->id,
			'cpf'     	=> $this->cpf,
			'cep'	  	=> $this->cep,
			'account'   => $this->account,
			'agency'    => $this->agency
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
