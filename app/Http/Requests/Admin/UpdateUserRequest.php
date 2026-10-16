<?php

namespace App\Http\Requests\Admin;

use App\Enums\Role;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends StoreUserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, ValidationRule|Closure|array<mixed>|string>>
     */
    public function rules(): array
    {
        /** @var User $account */
        $account = $this->route('user');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($account)],
            ...$this->roleRules(),
        ];

        // An Administrador cannot demote themselves, so the panel never ends up without one.
        $rules['role'][] = function (string $attribute, mixed $value, Closure $fail) use ($account): void {
            if ($account->is($this->user()) && $value !== Role::Administrador->value) {
                $fail('No puedes quitarte el rol de Administrador.');
            }
        };

        return $rules;
    }
}
