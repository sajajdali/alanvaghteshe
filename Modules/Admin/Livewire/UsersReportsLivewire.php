<?php

namespace Modules\Admin\Livewire;

use Carbon\Carbon;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Modules\Diet\Enum\DietRequestStatusEnum;
use Modules\User\Entities\User;
use Modules\Admin\app\Models\ActivityLog;
use Modules\Package\Enum\PackageUserTypeEnum;
use Modules\Admin\app\Enums\ActivityEventEnum;
use Modules\Admin\app\Enums\UserConditionFiltersEnum;

class UsersReportsLivewire extends Component
{
    use WithPagination;
    #[Url(as: 'filter', except: null, history: true)]
    public ?string $search = null;
    public ?User $user = null;
    public function callLogButtons(User $user)
    {
        if ($this->user->logFor->isEmpty()) {
            return '-';
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
    #[On('removeLog')]
    public function removeLog($model)
    {
        if (auth()->user()->isAdmin()) {
            try {
                $user = User::find($model);
                $user->lastSupporterCalled = "null" ;
                $this->dispatch('success',message :"اخرین تماس پشتیبان با موفقیت حذف شد");
            } catch (\Throwable $th) {
                $this->dispatch('error',message :"خطا در حذف");
            }
        }
    }
    public function startSearch()
    {
        $this->render();
        return;
    }
    public function assignUserForModal(User $user)
    {
        $this->user = $user;
    }
    #[On('callUser')]
    public function callUser($model)
    {
        $event = UserConditionFiltersEnum::tryFrom($this->search)->getEvent();
        $log = ActivityLog::log(null, $event, auth()->id(), $model, null);
        if (! is_null($this->user)) {
            $this->user->lastSupporterCalled = auth()->id();
        } else {
            User::find($model)->lastSupporterCalled = auth()->id();
        }
        return redirect()->route('admin.user.document', ['user' => $model, 'log' => $log->id , 'filter' => $this->search]);
    }
    protected function searchIn()
    {
        $query = null;
        if (! is_null($this->search)) {
            $query = User::query();
            $condition = UserConditionFiltersEnum::tryFrom($this->search);
            switch ($condition) {
                case UserConditionFiltersEnum::PACKAGE_END_ONE_DAY:
                    $query->whereHas('packages', function ($q) {
                        $q->where('type', PackageUserTypeEnum::IN_USE)->where('end_at', '<', Carbon::now()->addDay());
                    });
                    break;
                case UserConditionFiltersEnum::PACKAGE_END:
                    $query->whereHas('packages')
                        ->whereDoesntHave('packages', function ($q) {
                            $q->where('type', PackageUserTypeEnum::IN_USE);
                        });
                    break;
                case UserConditionFiltersEnum::PACKAGE_END_TEN_DAYS_AGO:
                    $query->whereHas('packages')
                        ->whereDoesntHave('packages', function ($q) {
                            $q->where('type', PackageUserTypeEnum::IN_USE);
                        })->whereHas('packages', function ($q) {
                            $q->where('type', PackageUserTypeEnum::END)
                                ->where('package_id' , '!=' ,'4')
                                ->orderByDesc('created_at')
                                ->where('end_at', '<=', now()->subDays(10));
                        });
                    break;
                case UserConditionFiltersEnum::PACKAGE_END_TEN_MONTH_AGO:
                    $query->whereHas('packages')
                        ->whereDoesntHave('packages', function ($q) {
                            $q->where('type', PackageUserTypeEnum::IN_USE);
                        })->whereHas('packages', function ($q) {
                            $q->where('type', PackageUserTypeEnum::END)
                                ->where('package_id' , '!=' ,'4')
                                ->orderByDesc('created_at')
                                ->where('end_at', '<=', now()->subMonth());
                        });
                    break;

                case UserConditionFiltersEnum::DIET_END_ONE_DAY:
                    $query->whereHas('dietRequests' , function ($q) {
                        $q->whereDate('end_date', Carbon::today()->toDateString());
                    });
                    break;
                case UserConditionFiltersEnum::DIET_END_YESTERDAY:
                    $query->whereHas('dietRequests' , function ($q) {
                        $q->where('status' , DietRequestStatusEnum::ACTIVE)->whereDate('end_date', Carbon::yesterday()->toDateString());
                    });
                    break;
                case UserConditionFiltersEnum::DIET_START_SEVEN_DAYS_AGO:
                    $query->whereHas('dietRequests' , function ($q) {
                        $q->where('status' , DietRequestStatusEnum::ACTIVE)->whereDate('start_date', Carbon::now()->subDays(7)->toDateString());
                    });
                    break;
                 case UserConditionFiltersEnum::DIET_START_FOURTEEN_DAYS_AGO:
                    $query->whereHas('dietRequests' , function ($q) {
                        $q->where('status' , DietRequestStatusEnum::ACTIVE)->whereDate('start_date', Carbon::now()->subDays(14)->toDateString());
                    });
                     break;
                case UserConditionFiltersEnum::DIET_END_ONE_WEEK_AGO:
                    $query->whereHas('dietRequests' , function ($q) {
                        $q->whereDate('end_date', Carbon::now()->addDay(5)->toDateString());
                    })->whereDoesntHave('dietRequests', function ($q) {
                        $q->where('start_date', '<=', now())
                            ->where('status' , DietRequestStatusEnum::ACTIVE)
                            ->where('end_date', '>=', now());
                    });;
                    break;
            }
        }
        return $query;
    }
    public function render()
    {
        $users = $this->searchIn()?->paginate(20);
        return view('admin::livewire.users-reports-livewire', ['users' => $users]);
    }
}
