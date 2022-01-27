<?php

namespace App\Http\Requests\ProcessHistory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

use App\Http\Utils\StatusCodeUtils;
use App\Models\ProcessHistory;

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
            'id'                 => 'required|integer',
            'process_history'    => 'required'
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
            "process_history.required"           => 'Histórico não encontrado.'
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

        $this->processHistory = ProcessHistory::find($this->id);

		$this->merge([
			'id'      	        => $this->id,
            'process_history'   => $this->processHistory
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
