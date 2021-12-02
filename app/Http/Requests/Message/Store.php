<?php

namespace App\Http\Requests\Message;

use App\Http\Utils\StatusCodeUtils;
use App\Models\Advocate;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
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
            'sender_name'       => 'required|string',
            'recipient_name'    => 'required|string',
            'subject'           => 'required|string',
            'message'           => 'required|string|max:500',
            'read'              => 'required|boolean',
            'client_send'       => 'required|boolean',
            'advocate_send'     => 'required|boolean',
            'user_id'           => 'required|integer',
        ];
    }

    /**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages()
	{
		return ['required' => 'O campo :attribute é obrigatório'];
	}

     /**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
        $recipientName = '';
        $user = User::where('user_id', $this->user_id)->first();
        
        if($user->is_client && $user->advocate_user_id) {
            $userAdvocate = User::where('advocate_user_id', $user->advocate_user_id)->first();
            $recipientName = $userAdvocate->name;
        }

        $this->merge([
			'recipient_name' =>  $recipientName
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
