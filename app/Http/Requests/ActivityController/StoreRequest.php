<?php

namespace App\Http\Requests\ActivityController;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
class StoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => 'required|string',
            'activitable_type' => 'required|string',
            'activitable_id' => 'required'
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        // Записываем ошибки в лог
        Log::error('Ошибка валидации в StoreRequest', [
            'errors' => $validator->errors()->toArray()
        ]);

        // Вызываем стандартное поведение (выброс исключения)
        throw new ValidationException($validator);
    }
}
