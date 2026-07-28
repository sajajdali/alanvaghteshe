<?php

namespace Modules\User\Livewire\Admin\User;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Setting\Enum\SettingKeyEnum;
use Modules\User\Entities\User;
use Modules\User\Entities\UserMeta;
use Modules\User\Enum\UserMetaEnum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserCreateOrUpdate extends Component
{
    use AuthorizesRequests;

    public ?User $user = null;

    public string $userName = '';

    public string $userLastName = '';

    public string $userEmail = '';

    public string $password = '';

    public string $password_confirmation = '';

    public array $selectedRoles = [];

    public string $userMobile = '';

    public string $userAvatar = '';
    public string $invitation_code = '';
    public int $referral_percentage = 10;

    public $adminRoles;

    public $userRoles;

    public $isEdited = false;

    public $supportTeam = [];

    public $supporter = [];
    public $applyToAllPayments = false;

    public function mount()
    {
        $this->userRoles = Permission::whereName('USER_ACCESS')->first()->roles;
        $this->adminRoles = Permission::whereName('ADMIN_ACCESS')->first()->roles;

        $supportUserRule = Role::firstWhere('id', setting(SettingKeyEnum::SUPPORT_USER_ROLE));
        if ($supportUserRule) {
            $supporterUser = $supportUserRule->users;
            if ($supporterUser->isNotEmpty()) {
                foreach ($supporterUser as $supportUser) {
                    $this->supportTeam[$supportUser->id] = $supportUser->full_name;
                }
            }
        }
        //get parameter user from request
        $user = request()->route('user');
        if ($user instanceof User) {
            $this->authorize('update', $user);
            $this->isEdited = true;
            $this->user = $user;
            $this->userName = $this->user?->first_name ?? '';
            $this->userLastName = $this->user?->last_name ?? '';
            $this->userEmail = $this->user?->email ?? '';
            $this->selectedRoles = $this->user?->roles->pluck('id')->toArray() ?? [];
            $this->userMobile = $this->user?->mobile ?? '';
            $this->userAvatar = $this->user?->avatar ?? '';
            $this->invitation_code = $this->user?->invitation_code ?? '';
            $this->applyToAllPayments = $this->user?->apply_to_all_payments ?? false;
            $this->referral_percentage = (int) $this->user?->referral_percentage ? $this->user?->referral_percentage : 10;
            //remove current user from supporter list
            unset($this->supportTeam[$this->user->id]);
            $this->supporter = $this->user?->supporter->pluck('id')->toArray() ?? [];
        }
        if ($this->user === null && auth()->user()?->cannot('user')) {
            //append user own role to selected roles
            $this->selectedRoles = [Role::whereHas('permissions', static function ($query) {
                $query->whereName('USER_DEFAULT');
            })->first()->id];
        }
    }

    protected function rules(): array
    {
        $rules = [
            'userName' => 'required',
            'userLastName' => 'required',
            'userEmail' => 'required|email|unique:users,email',
            'userMobile' => 'nullable|unique:users,mobile',
            'password' => 'required|confirmed',
            'selectedRoles' => 'required',
        ];
        if ($this->user !== null) { //if user is not null, then we are updating
            $rules['userEmail'] = 'required|email|unique:users,email,' . $this->user?->id;
            $rules['userMobile'] = 'nullable|unique:users,mobile,' . $this->user?->id;
            $rules['password'] = 'nullable|confirmed';
        }

        return $rules;
    }

    public function updateOrCreate()
    {
        $this->validate([
            'userEmail' => 'required|email',
            'userMobile' => 'required',
            'password' => $this->user ? 'nullable' : 'required',
            'invitation_code' => [
                'nullable',
                'string',
                Rule::unique('user_metas', 'meta_value')
                    ->where(function ($query) {
                        $query->where('meta_key', 'invitation_code');
                        if ($this->user) {
                            $query->whereNot('user_id', $this->user->id);
                        }
                    }),
            ],
        ]);

        // بررسی یکتا بودن invitation_code در user_metas
        if (!empty($this->invitation_code)) {
            $exists = UserMeta::where('meta_key', UserMetaEnum::INVITATION_CODE)
                ->where('meta_value', $this->invitation_code)
                ->whereHas('user', function ($query) {
                    if ($this->user) {
                        $query->where('id', '!=', $this->user->id);
                    }
                })
                ->exists();

            if ($exists) {
                $this->addError('invitation_code', 'این کد دعوت قبلاً استفاده شده است.');
                session()->flash('error', 'این کد دعوت قبلاً استفاده شده است.');
                return;
            }
        }

        if ($this->user === null) {
            /** @var User $user */
            $user = User::create([
                'email' => $this->userEmail,
                'mobile' => $this->userMobile,
                'password' => $this->password,
            ]);
        } else {
            /** @var User $user */
            $user = $this->user;
            $updatedFields = [
                'email' => $this->userEmail,
                'mobile' => $this->userMobile,
            ];
            if (!empty($this->password)) { //password update is optional
                $updatedFields['password'] = $this->password;
            }
            $user->update($updatedFields);
        }


        $user->first_name = $this->userName;
        $user->last_name = $this->userLastName;
        $user->invitation_code = $this->invitation_code;
        $user->referral_percentage = $this->referral_percentage;
        $user->apply_toAll_payments = $this->applyToAllPayments;
        if ($this->user === null) {
            $user->creator = auth()->id();
        }
        if (!empty($this->userAvatar) && filter_var($this->userAvatar, FILTER_VALIDATE_URL)) {
            $user->avatar = $this->userAvatar;
        }
        $roleNames = Role::whereIn('id', $this->selectedRoles)->pluck('name')->toArray();

        $user->syncRoles($roleNames);

        if ($this->supporter){
            $user->supporter()->sync($this->supporter);
        }
        session()->flash('success', 'کاربر با موفقیت اضافه شد ، لطفا اطلاعات وزنی و بدنی مربوط به این کاربر را وارد کنید.');
        return redirect()->route('admin.user.index');
    }

    public function render()
    {
        $title = (!is_null($this->user) && $this->user->exists) ? 'ویرایش کاربر' : 'ایجاد کاربر جدید';

        return view('user::livewire.admin.user.user-create-or-update')->title($title);
    }
}
