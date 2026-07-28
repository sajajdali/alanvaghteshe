<?php

namespace Modules\Package\Livewire\Admin;

use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Modules\User\Entities\User;
use Modules\User\Enum\UserMetaEnum;
use Modules\Package\Entities\Package;
use Modules\Package\Entities\PackageUser;

class PurchasedPackage extends Component
{
    use WithPagination;

    public $searchPanel = '';

    #[Url]
    public $search = [];

    public function startSearch()
    {
        $this->resetPage();
    }

    public function resetProperties()
    {
        $this->search = [];
        $this->searchPanel = '';
        $this->dispatch('closeCollaps',true);
        $this->resetPage();
    }
    public function render()
    {
        // 'diets' => DietRequest::whereNotNull('diet_plan_id')->orderByDesc('id')?->paginate(10),
        $packages = PackageUser::when(isset($this->search['id']) && (int) $this->search['id'] !== 0, function ($query) {
            return $query->where('id', 'LIKE', '%' . $this->search['id'] . '%');
        })->when(isset($this->search['packageName']), function ($query) {
            $packageid = Package::where('name', 'LIKE', '%' . $this->search['packageName'] . '%')->first()?->id ?? 0;
            return $query->where('package_id', $packageid);
        })->when(isset($this->search['UserName']), function ($query) {
            $user =  User::whereHas('metas', function ($q) {
                $q->where([
                    ['meta_key', UserMetaEnum::LAST_NAME],
                    ['meta_value', 'LIKE', "%{$this->search['UserName']}%"],
                ]) ;
            })->first()?->id ?? 0 ;
            return $query->where('user_id', $user);
        })->when(isset($this->search['userPhoneNumber']), function ($query) {
            $user =  User::where('mobile','LIKE' ,'%' . $this->search['userPhoneNumber'] . '%')->first()?->id ?? 0 ;
            return $query->where('user_id', $user);
        })->when(isset($this->search['startDate']), function ($query) {
            return $query->whereDate('start_at', Verta::parse($this->search['startDate'])->toCarbon()->toDateString());
        })->when(isset($this->search['endDate']), function ($query) {
            return $query->whereDate('end_at', Verta::parse($this->search['endDate'])->toCarbon()->toDateString());
        })->latest()->paginate(20);

        return view('package::livewire.admin.purchased-package', [
            'packages' => $packages
        ]);
    }
}
