<?php

namespace App\Http\Requests\Admin;

use App\Enums\RoleEnum;
use App\Models\PointOfSchool;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')],
            'school_id' => ['nullable', 'integer', Rule::exists('schools', 'id')],
            'point_of_school_ids' => ['array'],
            'point_of_school_ids.*' => ['integer', Rule::exists('point_of_schools', 'id')],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $role = Role::query()->find($this->integer('role_id'));

                if (! $role) {
                    return;
                }

                if ($role->name === RoleEnum::TANK_ADMIN) {
                    return;
                }

                if (! $this->filled('school_id')) {
                    $validator->errors()->add('school_id', 'A escola e obrigatoria para este perfil.');
                }

                $pointIds = collect($this->input('point_of_school_ids', []))
                    ->filter()
                    ->map(fn ($pointId) => (int) $pointId)
                    ->values();

                if ($pointIds->isEmpty()) {
                    $validator->errors()->add('point_of_school_ids', 'Selecione pelo menos um ponto de ensino para este usuario.');

                    return;
                }

                if ($role->name === RoleEnum::STUDENT && $pointIds->count() > 1) {
                    $validator->errors()->add('point_of_school_ids', 'Usuarios do tipo Student podem ser vinculados a apenas um ponto de ensino.');
                }

                $validPointsCount = PointOfSchool::query()
                    ->where('school_id', $this->integer('school_id'))
                    ->whereIn('id', $pointIds)
                    ->count();

                if ($validPointsCount !== $pointIds->count()) {
                    $validator->errors()->add('point_of_school_ids', 'Selecione apenas pontos de ensino vinculados a escola escolhida.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do usuario e obrigatorio.',
            'email.required' => 'O e-mail do usuario e obrigatorio.',
            'email.email' => 'Informe um e-mail valido.',
            'email.unique' => 'Este e-mail ja esta em uso.',
            'role_id.required' => 'O perfil do usuario e obrigatorio.',
            'point_of_school_ids.array' => 'Os pontos de ensino informados sao invalidos.',
            'status.required' => 'O status do usuario e obrigatorio.',
        ];
    }
}
