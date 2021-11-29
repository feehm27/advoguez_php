<?php

namespace App\Http\Requests\Client;

use App\Http\Utils\HeaderPDFUtils;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

use App\Http\Utils\StatusCodeUtils;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class Download extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (Auth::user()->is_advocate == 1) return true;

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
            'id'            => 'required_without:all_clients|integer',
            'client'        => 'required_without:clients',
            'all_clients'   => 'required',
            'clients'       => 'required_without:id',
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
            'required_without' => 'O campo ::attribute é obrigatório',
            'required' => 'O campo :attribute é obrigatório'
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
        
        $this->client = null;
        $this->clients = [];

        if($this->id){
            $this->client = Client::find($this->id);
        }

        if(!$this->all_clients){
            $this->all_clients = false;
        }
	
        if($this->all_clients) {
            $this->clients = Client::where('advocate_user_id', $this->user->id)->get(HeaderPDFUtils::ATTRIBUTES_CLIENT);
        }
     
		$this->merge([
            'user'          => $this->user,
            'client'        => $this->client,
            'clients'       => $this->clients,
            'all_clients'   => $this->all_clients
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
