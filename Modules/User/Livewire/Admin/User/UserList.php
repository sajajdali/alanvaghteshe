<?php

namespace Modules\User\Livewire\Admin\User;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Modules\User\Entities\User;
use Modules\User\Enum\UserMetaEnum;
use Modules\Setting\Enum\SettingKeyEnum;
use Modules\Admin\app\Models\ActivityLog;
use Modules\Admin\app\Service\ActivityLogger;
use Modules\Admin\app\Enums\ActivityEventEnum;
use Modules\User\Notifications\UserSmsNotification;

#[Title('مدیریت کاربران')]
class UserList extends Component
{
    use WithPagination;

    #[Url]
    public $search = [];

    public $searchPanel = '';

    #[On('delete')]
    public function delete(User $model)
    {
        $this->authorize('delete', $model);

        try {
            $model->delete();
        } catch (\Exception $e) {
        }

        return redirect()->route('admin.user.index')->with('success', 'کاربر با موفقیت حذف شد.');
    }

    public function startSearch()
    {
        $this->resetPage();
    }
    public function sendWebAppLink(User $user)
    {
        $template = setting(SettingKeyEnum::SMS_SEND_APP_LINK);
        if (! is_null($template)) {
            $user->notify(new UserSmsNotification($template));
            ActivityLog::log(null, ActivityEventEnum::SEND_APP_SMS_LINK, auth()->id(), $user->id, null);
            $this->dispatch('success', message: "لینک ارسال شد");
        } else {
            $this->dispatch('error', message: "قالب پیامکی تعریف نشده");
        }
    }
    public function resetSearch()
    {
        unset($this->search);
        return;
    }
    public function render()
    {
        $users = User::when(auth()->user()?->cannot('user'), function ($query) {
            $query->whereHas('supporter', function ($q2) {
                $q2->where('support_id', auth()->id());
            });
        })
            ->when(isset($this->search['id']) && (int) $this->search['id'] !== 0, function ($query) {
                return $query->where('id', $this->search['id']);
            })
            ->when(isset($this->search['mobile']) && ! empty($this->search['mobile']), function ($query) {
                return $query->where('mobile', 'LIKE', "%{$this->search['mobile']}%");
            })
            ->when(isset($this->search['email']) && ! empty($this->search['email']), function ($query) {
                return $query->where('email', 'LIKE', "%{$this->search['email']}%");
            })
            ->when(isset($this->search['first_name']) && ! empty($this->search['first_name']), function ($query) {
                return $query->whereHas('metas', function ($q) {
                    $q->where([
                        ['meta_key', UserMetaEnum::FIRST_NAME],
                        ['meta_value', 'LIKE', "%{$this->search['first_name']}%"],
                    ]);
                });
            })
            ->when(isset($this->search['last_name']) && ! empty($this->search['last_name']), function ($query) {
                return $query->whereHas('metas', function ($q) {
                    $q->where([
                        ['meta_key', UserMetaEnum::LAST_NAME],
                        ['meta_value', 'LIKE', "%{$this->search['last_name']}%"],
                    ]);
                });
            })
            ->orderByDesc('id')->paginate(10);

        return view('user::livewire.admin.user.user-list', compact('users'));
    }
}
