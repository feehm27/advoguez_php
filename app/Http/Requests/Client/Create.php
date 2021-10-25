<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

use App\Http\Utils\StatusCodeUtils;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Create extends FormRequest
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
            'name'              => 'required|string|max:200',
            'email'             => 'required|string|email|max:255|unique:clients',
            'cpf'               => 'required|string|max:11|unique:clients',
            'rg'                => 'required|string|max:10',
            'issuing_organ'     => 'required|string|max:50',
            'nationality'       => 'required|string|max:200',
            'birthday'          => 'nullable',
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
            'name_user'         => 'required|string|max:255',
            'email_user'        => 'required|string|email|max:255',
            'password_user'     => 'required|string|min:8',
            'is_client'         => 'required|boolean',
            'is_advocate'       => 'required|boolean',
			'facebook_id'       => 'nullable|string',
            'advocate_user_id'  => 'required',
            'has_user'          => 'required|boolean'
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

        $characters = array('.', '/', '-', '(' , ')', ' ');

        if ($this->cpf) {
			$this->cpf = str_replace($characters, '', $this->cpf);
		}

        if ($this->telephone) {
			$this->telephone = str_replace($characters, '', $this->telephone);
		}

        if ($this->cellphone) {
			$this->cellphone = str_replace($characters, '', $this->cellphone);
		}

        if ($this->cep) {
			$this->cep = str_replace($characters, '', $this->cep);
		}

        $hasUser = false; 

        if($this->email_user){
            $user = User::where('email', $this->email_user)->first();
            if($user) $hasUser = true;
        }

		$this->merge([
            'advocate_user_id' 	=> $this->user->id,
            'password_user'     => Hash::make($this->password_user),
            'is_client'         => 1,
            'is_advocate'       => 0,
            'facebook_id'       => null,
            'cpf'               => $this->cpf,
            'telephone'         => $this->telephone,
            'cellphone'         => $this->cellphone,
            'cep'               => $this->cep,
            'has_user'          => $hasUser
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
