<?php

namespace App\Http\Requests\API\TaskController;

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
            'project_id' => 'required|exists:projects,id',
            'column_id' => 'required|exists:columns,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'nullable|date_format:Y-m-d',
            'time' => 'nullable|date_format:H:i',
            'old_files' => 'nullable|array',
            'files' => 'nullable|array',
            'files.*' => 'required|file|max:50240',
            'reminder' => 'nullable|date_format:H:i',
            'responsible' => 'nullable|array',
            'responsible.*' => 'required|exists:users,id'
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'Ошибка',
            'project_id.exists' => 'Ошибка',
            'column_id.exists' => 'Ошибка',
            'column_id.required' => 'Ошибка',
            'title.required' => 'Укажите название',
            'title.string' => 'Название должно быть строкой',
            'title.max' => 'Название не должно превышать 255 символов',
            'description.string' => 'Описание должно быть строкой',
            'date.date_format' => 'Неверный формат даты',
            'time.date_format' => 'Неверный формат времени',
            'files.array' => 'Загружаемые файлы должны быть массивом.',
            'files.*.required' => 'Каждый файл является обязательным для загрузки.',
            'files.*.file' => 'Загружаемый элемент должен быть файлом.',
            'files.*.max' => 'Размер каждого файла не должен превышать 50 МБ.',
            'reminder.date_format' => 'Неверный формат напоминания',
            'responsible.array' => 'Ответственные должны быть массивом',
            'responsible.*.exists' => 'Указанный ответственный не существует'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // Записываем ошибки в лог
        \Log::error('Ошибка валидации в StoreRequest', [
            'errors' => $validator->errors()->toArray()
        ]);
    }
}
