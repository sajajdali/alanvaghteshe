<?php

namespace Modules\User\Livewire\Admin\User;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\User\app\Models\InviteFriend;
use Modules\User\Enum\UserMetaEnum;

class ReferralList extends Component
{
    use withPagination;
    #[Url]
    public $search = [];

    public $searchPanel = '';

    public function startSearch()
    {
        $this->resetPage();
    }
    public function render()
    {
        if (request()->has('inviteFriend')){
            $this->search['user_id'] = request()->inviteFriend;
        }
        if (request()->has('payment') && request()->payment == '1'){
        }
        $inviteFriends = InviteFriend::when(isset($this->search['id']) && (int) $this->search['id'] !== 0, function ($query) {
            return $query->where('id', $this->search['id']);
        })
            ->when(isset($this->search['user_id']) && ! empty($this->search['user_id']), function ($query) {
                return $query->where('user_id', "{$this->search['user_id']}");
            })

            ->when(request()->has('payment') && request()->payment == '1', function ($query) {
                return $query->where('benefit', '>' , 0);
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
            ->orderByDesc('id')->paginate(100);
        return view('user::livewire.admin.user.referral-list' , compact('inviteFriends'));
    }
}
