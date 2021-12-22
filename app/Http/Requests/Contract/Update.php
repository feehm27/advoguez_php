<?php

namespace App\Http\Requests\Contract;

use Illuminate\Support\Facades\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

use App\Http\Utils\StatusCodeUtils;
use App\Models\Contract;

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
            'start_date'        => 'required|date|date_format:YYYY-MM-DD',
            'finish_date'       => 'required|date|date_format:YYYY-MM-DD',
            'payment_day'       => 'required|integer',
            'contract_price'    => 'required|string',
            'fine_price'        => 'required|string',
            'agency'            => 'required|string',
            'account'           => 'required|string',
            'bank'              => 'required|string',
            'client_id'         => 'required|integer',
            'advocate_id'       => 'required|integer',
            'contract'          => 'required'
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
			'required'          => "O campo :attribute é obrigatório",
            'contract.required' => "Contrato não encontrado"
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
        
        $this->id = $this->route('id');
		$this->contract = Contract::find($this->id);

		$this->merge([
			'contract' 	=> $this->contract,
            'id'        => $this->id,
            'user'      => $this->user
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
