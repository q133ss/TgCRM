<?php

namespace App\Http\Requests\Admin\LoginController;

use App\Models\User;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'username' => 'required|string',
            'password' => [
                'required',
                'string',
                function (string $attribute, mixed $value, Closure $fail): void
                {
                    $password = 'password';
                    $user = User::where('username', $this->username)->first();
                    if(!$user || $value != $password)
                    {
                        $fail('Неверный логин или пароль');
                    }

                    if($user->is_admin != true){
                        $fail('У вас недостаточно прав');
                    }
                }
            ]
        ];
    }
}
