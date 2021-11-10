<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

use App\Http\Utils\StatusCodeUtils;
use App\Models\User;

class Destroy extends FormRequest
{
     /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->authUser->is_advocate == 1) return true;

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
            'id'          => 'required|integer',
            'user'        => 'required'
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
            "user.required"           => 'Usuário não encontrado.'
        ];
	}

    /**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
		$this->authUser = Auth::user();
        $this->user = User::find($this->id);
        
		$this->merge([
			'id'      => $this->id,
            'user'    => $this->user
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
