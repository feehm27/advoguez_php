<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

use App\Http\Utils\StatusCodeUtils;
use App\Models\Contract;

class Destroy extends FormRequest
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
            'id'             => 'required|integer',
            'contract'        => 'required'
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
            "client.required"           => 'Cliente não encontrado.'
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
			'id'      	=> $this->id,
            'contract'  => $this->contract
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
