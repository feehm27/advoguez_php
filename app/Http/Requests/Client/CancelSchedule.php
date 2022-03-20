<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

use App\Http\Utils\StatusCodeUtils;
use App\Models\User;

class CancelSchedule extends FormRequest
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
            'date'                => 'required|date|date_format:Y-m-d',
            'horarys'             => 'required|array',
			'client_id'           => 'required|integer',
            'advocate_user_id'    => 'required|integer',
            'advocate_name'       => 'required|string',
            'email'               => 'required|string'
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
			'required'         => "O campo :attribute é obrigatório",
		];
	}

    /**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
        $this->email = null;

		$advocateUser = User::find($this->advocate_user_id);

        if($advocateUser){
            $this->email            = $advocateUser->email;
            $this->advocate_name    = $advocateUser->name;
        }

        $this->merge([
            'advocate_name' => $this->advocate_name,
            'email'         => $this->email
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
