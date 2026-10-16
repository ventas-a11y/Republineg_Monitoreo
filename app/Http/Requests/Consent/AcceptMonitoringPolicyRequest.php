<?php

namespace App\Http\Requests\Consent;

use App\Models\MonitoringPolicy;
use Closure;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AcceptMonitoringPolicyRequest extends FormRequest
{
    /**
     * Only Empleados accept the Política de monitoreo; anyone else gets a 404.
     */
    public function authorize(): Response
    {
        return $this->user()?->mustAcceptMonitoringPolicy()
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * The submitted id must be the version in force: if a new one was published
     * while the Empleado was reading, they must read the new one first.
     *
     * @return array<string, array<int, ValidationRule|Closure|string>>
     */
    public function rules(): array
    {
        return [
            'accept' => ['accepted'],
            'monitoring_policy_id' => [
                'required',
                'ulid',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ($value !== MonitoringPolicy::current()?->id) {
                        $fail('Se publicó una versión nueva de la política. Léela antes de aceptarla.');
                    }
                },
            ],
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
            'accept.accepted' => 'Marca la casilla para confirmar que leíste la política.',
        ];
    }
}
