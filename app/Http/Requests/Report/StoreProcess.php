<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

use App\Http\Utils\StatusCodeUtils;
use App\Models\ProcessReport;
use App\Models\Report;

class StoreProcess extends FormRequest
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
			'id'                    => 'nullable|integer',
            'start_date'            => 'nullable|date_format:Y-m-d',
            'end_date'              => 'nullable|date_format:Y-m-d',
            'status'                => 'nullable|string',
			'report_id'             => 'required|integer',
			'report'                => 'required',
            'advocate_user_id'      => 'required',
			'process_report'        => 'nullable',
			'user_id'				=> 'required'
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
		$this->processReport = null;
		$this->user = Auth::user();

		if($this->id){
			$this->processReport = ProcessReport::find($this->id);
		}

		$report = Report::find($this->report_id);

        $this->merge([
			'advocate_user_id' 	 => $this->user->id,
			'process_report'     => $this->processReport,
			'report'             => $report,
			'user_id'            => $this->user->id
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
