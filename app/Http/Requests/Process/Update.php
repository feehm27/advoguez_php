<?php

namespace App\Http\Requests\Process;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

use App\Http\Utils\StatusCodeUtils;
use App\Models\Process;

class Update extends FormRequest
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
			'required'          => "O campo :attribute é obrigatório",
			'image.mimes'       => "Formato do arquivo inválido.",
            'process.required'  => "Processo não encontrado."
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
		$this->process = Process::find($this->id);

        $characters = array('.', '/', '-', ',');

        if($this->values) {

            $this->values = json_decode($this->values);
            $this->number = $this->values->number;
            $this->labor_stick = $this->values->labor_stick;
            $this->petition = $this->values->petition;
            $this->status = $this->values->status;
            $this->start_date = $this->values->start_date;
            $this->start_date = $this->values->start_date;
          
            if(isset($this->values->end_date)){
                $this->end_date = $this->values->end_date;
            }else{
                $this->end_date = null;
            }

            $this->observations = $this->values->observations;
        }

        if ($this->number) {
            $this->number = str_replace($characters, '', $this->number);
		}

        $this->merge([
            'id'                => $this->id,
            'number'            => $this->number,
            'process'           => $this->process,
            'user'              => $this->user,
            'number'            => $this->number,
            'labor_stick'       => $this->labor_stick,
            'petition'          => $this->petition,
            'status'            => $this->status,
            'start_date'        => $this->start_date,
            'end_date'          => $this->end_date,
            'observations'      => $this->observations,
            'user'              => $this->user,
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
