<?php

namespace App\Http\Requests\TaskController;

use App\Models\Column;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class MoveRequest extends FormRequest
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
            'column_id' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail): void
                {
                    $column = Column::find($value);
                    if(!$column || $column->project_id != $this->project_id){
                        $fail('Ошибка');
                    }
                }
            ]
        ];
    }
}
