<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TimeLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $status = $this->input('status') ?? 'start';

        return [
            'project_id' => 'nullable|exists:projects,id',
            'start_time' => [
                $status == 'log' ? 'required' : 'nullable',
                'date_format:Y-m-d H:i:s',
            ],
            'end_time' => [
                $status == 'log' ? 'required' : 'nullable',
                'date_format:Y-m-d H:i:s',
                'after:start_time',
            ],
            'notes' => 'nullable|string|min:3|max:255',
            'status' => 'required|in:start,end,log'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errorResponse = [
            'msg'           =>  'Validation error',
            'errors'        =>  $validator->errors()
        ];
        throw new HttpResponseException(response()->json($errorResponse, 422));
    }
}
