<?php

namespace App\Http\Requests\Auth;

use App\Concerns\PasswordValidationRules;
use Closure;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TemporaryPasswordUpdateRequest extends FormRequest
{
    use PasswordValidationRules;

    /**
     * Only a user with a pending temporary password may use this endpoint;
     * anyone else gets a 404 before any validation runs.
     */
    public function authorize(): Response
    {
        return $this->user()?->password_change_required
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, Password|ValidationRule|Closure|array<mixed>|string>>
     */
    public function rules(): array
    {
        return [
            'password' => [
                ...$this->passwordRules(),
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (is_string($value) && Hash::check($value, (string) $this->user()?->password)) {
                        $fail('La nueva contraseña debe ser distinta de la temporal.');
                    }
                },
            ],
        ];
    }
}
