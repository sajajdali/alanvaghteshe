<?php

namespace Modules\Package\Livewire\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Package\Entities\Package;
use Modules\Package\Enum\PackageTypeEnum;

#[title('بسته‌ها')]
class PackageList extends Component
{
    use AuthorizesRequests, withPagination;

    #[Url]
    public array $search = [];

    public $searchPanel = '';

    #[On('delete')]
    public function delete(Package $model)
    {
        $this->authorize('delete', $model);
        try {
            $model->delete();
        } catch (\Exception $e) {
        }

        return redirect()->route('admin.package.index')->with('success', 'بسته با موفقیت حذف شد');
    }

    public function setSuggested($packageId)
    {
        $sugestedPackage = Package::where('detail->'.Package::JSON_DETAIL_SUGGESTED, true)->first();
        if ($sugestedPackage){
//            $detail = $sugestedPackage->detail;
//            unset($detail[Package::JSON_DETAIL_SUGGESTED]);
//            $sugestedPackage->update(['detail' => $detail]);
        }
        $package = Package::find($packageId);
        $detail = $package->detail;
        if (isset($package->detail[Package::JSON_DETAIL_SUGGESTED])){
            unset($detail[Package::JSON_DETAIL_SUGGESTED]);
        } else {
            $detail[Package::JSON_DETAIL_SUGGESTED] = true;
        }
        $package->update(['detail' => $detail]);
    }

    public function startSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {

        return view('package::livewire.admin.package-list', [
            'packages' => Package::when(isset($this->search['id']) && (int) $this->search['id'] !== 0,
                function ($query) {
                    return $query->where('id', $this->search['id']);
                })->when(isset($this->search['name']) && ! empty($this->search['name']), function ($query) {
                    return $query->where('name', 'LIKE', '%'.$this->search['name'].'%');
                })->when(isset($this->search['type']) && ! empty($this->search['type']), function ($query) {
                    return $query->where('type', PackageTypeEnum::tryFrom($this->search['type']));
                })->when(isset($this->search['days']) && ! empty($this->search['days']), function ($query) {
                    return $query->where('days', $this->search['days']);
                })->when(isset($this->search['status']) && ! empty($this->search['status']), function ($query) {
                    return $query->where('is_active', (bool) $this->search['status']);
                })->paginate(20),
        ]);
    }
}
