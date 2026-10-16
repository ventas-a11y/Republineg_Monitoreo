<?php

namespace App\Http\Requests\Admin;

use App\Enums\Role;
use App\Models\Area;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            ...$this->roleRules(),
        ];
    }

    /**
     * Role plus the Área fields it needs: one Área for an Empleado, one or more
     * reviewed Áreas for a Revisor, none for an Administrador.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function roleRules(): array
    {
        return [
            'role' => ['required', Rule::enum(Role::class)],
            'area_id' => ['exclude_unless:role,'.Role::Empleado->value, 'required', 'ulid', Rule::exists(Area::class, 'id')],
            'reviewed_area_ids' => ['exclude_unless:role,'.Role::Revisor->value, 'required', 'array', 'min:1'],
            'reviewed_area_ids.*' => ['exclude_unless:role,'.Role::Revisor->value, 'ulid', 'distinct', Rule::exists(Area::class, 'id')],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'area_id.required' => 'Un Empleado debe pertenecer a un Área.',
            'reviewed_area_ids.required' => 'Un Revisor debe tener al menos un Área asignada.',
            'reviewed_area_ids.min' => 'Un Revisor debe tener al menos un Área asignada.',
        ];
    }
}
