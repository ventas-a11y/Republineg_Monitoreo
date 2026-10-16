<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Accounts\IssueTemporaryPassword;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Area;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Account management for Administradores. Authorization lives on the routes
 * (UserPolicy through the `can` middleware).
 */
class UserController extends Controller
{
    /**
     * List every account with its role, Áreas and onboarding state.
     */
    public function index(): Response
    {
        $users = User::query()
            ->with(['area:id,name', 'reviewedAreas:id,name', 'passkeys:id,user_id'])
            ->orderBy('name')
            ->get()
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'role_label' => $user->role->label(),
                'areas' => $user->role === Role::Revisor
                    ? $user->reviewedAreas->pluck('name')->all()
                    : array_filter([$user->area?->name]),
                'has_second_factor' => $user->two_factor_confirmed_at !== null || $user->passkeys->isNotEmpty(),
                'invitation_pending' => $user->password_change_required,
                'invitation_expired' => $user->hasExpiredTemporaryPassword(),
            ]);

        return Inertia::render('admin/users/index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form to create an account.
     */
    public function create(): Response
    {
        return Inertia::render('admin/users/create', $this->formOptions());
    }

    /**
     * Create the account and email its invitation with a temporary password.
     */
    public function store(StoreUserRequest $request, IssueTemporaryPassword $issueTemporaryPassword): RedirectResponse
    {
        DB::transaction(function () use ($request, $issueTemporaryPassword): void {
            $user = new User($request->safe()->only(['name', 'email', 'role']));
            $user->area_id = $request->validated('area_id');

            $issueTemporaryPassword->handle($user);

            $user->reviewedAreas()->sync($request->validated('reviewed_area_ids', []));
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Cuenta creada. Se envió la invitación por correo.']);

        return redirect()->route('admin.users.index');
    }

    /**
     * Show the form to edit an account.
     */
    public function edit(User $user): Response
    {
        return Inertia::render('admin/users/edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'area_id' => $user->area_id,
                'reviewed_area_ids' => $user->reviewedAreas()->pluck('areas.id')->all(),
                'invitation_pending' => $user->password_change_required,
            ],
            ...$this->formOptions(),
        ]);
    }

    /**
     * Update the account's data, role and Áreas.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        DB::transaction(function () use ($request, $user): void {
            $user->fill($request->safe()->only(['name', 'email', 'role']));
            $user->area_id = $request->validated('area_id');

            // A new address must be verified again by its owner.
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            $user->reviewedAreas()->sync($request->validated('reviewed_area_ids', []));
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Cuenta actualizada.']);

        return redirect()->route('admin.users.index');
    }

    /**
     * Issue a new temporary password, e.g. when the previous one expired.
     */
    public function resendInvitation(User $user, IssueTemporaryPassword $issueTemporaryPassword): RedirectResponse
    {
        $issueTemporaryPassword->handle($user);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Se envió una nueva invitación a '.$user->email.'.']);

        return back();
    }

    /**
     * Roles and Áreas offered by the account form.
     *
     * @return array{roles: list<array{value: string, label: string}>, areas: list<array{id: string, name: string}>}
     */
    private function formOptions(): array
    {
        return [
            'roles' => array_map(
                fn (Role $role): array => ['value' => $role->value, 'label' => $role->label()],
                Role::cases(),
            ),
            'areas' => array_values(Area::query()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (Area $area): array => ['id' => $area->id, 'name' => $area->name])
                ->all()),
        ];
    }
}
