<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

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
            'id'                => 'required|integer',
            'name'              => 'required|string|max:200',
            'email'             => 'required|string|email|max:255|unique:clients,email,' . $this->id,
            'cpf'               => 'required|string|max:11|unique:clients,cpf,' . $this->id,
            'rg'                => 'required|string|max:10',
            'issuing_organ'     => 'required|string|max:50',
            'nationality'       => 'required|string|max:200',
            'birthday'          => 'required|date|date_format:Y-m-d',
            'gender'            => 'required|string|max:50',
            'civil_status'      => 'required|string|max:50',
            'telephone'         => 'nullable|string|max:10',
            'cellphone'         => 'required|string|max:11',
            'cep'               => 'required|string|max:8',
            'street'            => 'required|string|max:200',
            'number'            => 'required|integer',
            'complement'        => 'nullable|string|max:200',
            'district'          => 'required|string|max:150',
            'state'             => 'required|string|max:200',
            'city'              => 'required|string|max:200',
            'advocate_user_id'  => 'required',
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

		$this->user = Auth::user();

        $characters = array('.', '/', '-', '(' , ')');

        if ($this->cpf) {
			$this->cpf = str_replace($characters, '', $this->cpf);
		}

        if ($this->cep) {
			$this->cep = str_replace($characters, '', $this->cep);
		}

        if ($this->telephone) {
			$this->telephone = str_replace($characters, '', $this->telephone);
		}

        if ($this->cellphone) {
			$this->cellphone = str_replace($characters, '', $this->cellphone);
		}

		$this->merge([
            'id'  => $this->id,
            'cpf' => $this->cpf,
            'cep' => $this->cep,
            'telephone' => $this->telephone,
            'cellphone' => $this->cellphone,
			'advocate_user_id' 	=> $this->user->id,
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
