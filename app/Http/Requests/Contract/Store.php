<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

use App\Http\Utils\StatusCodeUtils;

class Store extends FormRequest
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
            'start_date'        => 'required|date_format:Y-m-d',
            'finish_date'       => 'required|date_format:Y-m-d',
            'payment_day'       => 'required|string',
            'contract_price'    => 'required|string',
            'fine_price'        => 'required|string',
            'agency'            => 'required|string',
            'account'           => 'required|string',
            'bank'              => 'required|string',
            'client_id'         => 'required|integer',
            'advocate_id'       => 'required|integer',
            'advocate_user_id'  => 'required|integer'
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
			'required' => "O campo :attribute é obrigatório",
		];
	}

    /**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
        $characters = array('.', '/', '-', 'R$', ',', ' ');

        if ($this->contract_price) {
            $this->contract_price = str_replace($characters,'', $this->contract_price);
            $this->contract_price = preg_replace('/[^a-zA-Z0-9]/','', $this->contract_price);
		}
        
        if ($this->fine_price) {
            $this->fine_price = str_replace($characters, '', $this->fine_price);
            $this->fine_price = preg_replace('/[^a-zA-Z0-9]/','', $this->fine_price);
		}

		$this->user = Auth::user();
        
		$this->account = str_replace($characters, '', $this->account);
		$this->agency = str_replace($characters, '', $this->agency);

        $this->merge([
			'advocate_user_id' 	=> $this->user->id,
            'account'           => $this->account,
            'agency'            => $this->agency,
            'contract_price'    => $this->contract_price,
            'fine_price'        => $this->fine_price
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
