<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Orchid\Layouts\Role\RolePermissionLayout;
use App\Orchid\Layouts\User\UserEditLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Orchid\Access\UserSwitch;
use Orchid\Platform\Models\Role;
use Orchid\Platform\Models\User;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Password;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layout;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class UserEditScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'User';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Details such as name, email and password';

    /**
     * @var string
     */
    public $permission = 'platform.systems.users';

    /**
     * @var bool
     */
    private $exist = false;

    /**
     * Query data.
     *
     * @param User $user
     *
     * @return array
     */
    public function query(User $user): array
    {
        $this->exist = $user->exists;

        $user->load(['roles']);

        return [
            'user' => $user,
            'permission' => $user->getStatusPermission(),
        ];
    }

    /**
     * Button commands.
     *
     * @return Action[]
     */
    public function commandBar(): array
    {
        $modal = $this->exist
            ? [
                DropDown::make(__('Settings'))
                    ->icon('icon-open')
                    ->list([
                        Button::make(__('Login as user'))
                            ->icon('icon-login')
                            ->method('loginAs'),
                        ModalToggle::make(__('Change Password'))
                            ->icon('icon-lock-open')
                            ->method('changePassword')
                            ->modal('password')
                            ->title(__('Change Password')),
                    ]),
            ]
            : [];
        $save = [
            Button::make(__('Save'))
                ->icon('icon-check')
                ->method('save'),
        ];
        $remove = $this->exist
            ? [
                Button::make(__('Remove'))
                    ->icon('icon-trash')
                    ->confirm('Are you sure you want to delete the user?')
                    ->method('remove'),
            ]
            : [];
        return array_merge($modal, $save, $remove);
    }

    /**
     * @return Layout[]
     * @throws \Throwable
     *
     */
    public function layout(): array
    {
        return [
            Layout::rows(array_merge(
                [
                    Input::make('user.name')
                        ->type('text')
                        ->max(255)
                        ->required()
                        ->title(__('Name'))
                        ->placeholder(__('Name')),

                    Input::make('user.email')
                        ->type('email')
                        ->required()
                        ->title(__('Email'))
                        ->placeholder(__('Email')),
                ],
                !$this->exist
                    ? [
                    Password::make('user.password')
                        ->placeholder(__('Enter your password'))
                        ->required()
                        ->title(__('Password')),
                ]
                    : [],
                [
                    Select::make('user.roles.')
                        ->fromModel(Role::class, 'name')
                        ->multiple()
                        ->title(__('Name role'))
                        ->help('Specify which groups this account should belong to'),
                ]
            )),

            Layout::rubbers([
                RolePermissionLayout::class,
            ]),

            Layout::modal('password', [
                Layout::rows([
                    Password::make('password')
                        ->placeholder(__('Enter your password'))
                        ->required()
                        ->title(__('Password')),
                ]),
            ]),
        ];
    }

    /**
     * @param User $user
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(User $user, Request $request)
    {
        $permissions = collect($request->get('permissions'))
            ->map(function ($value, $key) {
                return [base64_decode($key) => $value];
            })
            ->collapse()
            ->toArray();

        $user
            ->fill($request->get('user'))
            ->replaceRoles($request->input('user.roles'))
            ->fill([
                'permissions' => $permissions,
            ])
            ->save();

        Toast::info(__('User was saved.'));

        return redirect()->route('platform.systems.users');
    }

    /**
     * @param User $user
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     *
     */
    public function remove(User $user)
    {
        $user->delete();

        Toast::info(__('User was removed'));

        return redirect()->route('platform.systems.users');
    }

    /**
     * @param User $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginAs(User $user)
    {
        UserSwitch::loginAs($user);

        return redirect()->route(config('platform.index'));
    }

    /**
     * @param User $user
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(User $user, Request $request)
    {
        $user->password = Hash::make($request->get('password'));
        $user->save();

        Toast::info(__('User was saved.'));

        return back();
    }
}
