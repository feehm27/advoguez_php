<?php

namespace App\Http\Requests\Process;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

use App\Http\Utils\StatusCodeUtils;
use App\Models\Process;

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
            'number'            => 'required|string',
            'labor_stick'       => 'required|string',
            'petition'          => 'required|string',
            'status'            => 'required|string',
            'file'              => 'nullable|mimes:pdf',
            'start_date'        => 'required|date_format:Y-m-d',
            'end_date'          => 'nullable|date_format:Y-m-d',
            'observations'      => 'nullable|string',
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
			'image.mimes'      => "Formato do arquivo inválido.",
		];
	}

    /**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
        $characters = array('.', '/', '-', ',');

        if ($this->number) {
            $this->number = str_replace($characters, '', $this->number);
		}

		$this->user = Auth::user();

        $this->merge([
			'number'        => $this->number,
            'user'          => $this->user,
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
