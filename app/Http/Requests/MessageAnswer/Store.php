<?php

namespace App\Http\Requests\MessageAnswer;

use App\Http\Utils\StatusCodeUtils;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class Store extends FormRequest
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
			'answer'            	=> 'required|string',
            'code_message'      	=> 'required|integer',
			'message_received_id'	=> 'required|integer',
			'response_client'       => 'nullable|boolean',
			'response_advocate'     => 'nullable|boolean',
            'advocate_user_id'  	=> 'required|integer',
		];
    }

    /**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
		if(!$this->advocate_user_id){

			$this->user = Auth::user();

			$this->merge([
				'advocate_user_id' =>  $this->user->id
			]);
		}
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
