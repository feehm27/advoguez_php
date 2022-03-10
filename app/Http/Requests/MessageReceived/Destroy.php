<?php

namespace App\Http\Requests\MessageReceived;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

use App\Http\Utils\StatusCodeUtils;
use App\Models\Client;
use App\Models\MessageReceived;

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
            'id'                => 'required_without:all_messages|nullable|integer',
            'client_id'         => 'required_with:all_messages|integer',
            'all_messages'      => 'nullable|boolean',
            'message_received'  => 'required_without:all_messages|nullable',
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
            'required'  => "O campo :attribute é obrigatório",
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
        $this->messageReceived = null;

        if($this->id) {
            $this->messageReceived = MessageReceived::find($this->id);
        }
	
		$this->merge([
			'id'                => $this->id,
            'message_received'  => $this->messageReceived 
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
