<?php

namespace Modules\Package\Livewire\Admin;

use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Onboarding\Entities\OnboardingReward;

#[Title('بسته‌های رایگان اهدایی')]
class GiftedPackage extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $term = trim($this->search);

        $packages = OnboardingReward::query()
            ->with(['user.metas', 'package', 'packageUser'])
            ->when($term !== '', fn ($query) => $query->where(function ($query) use ($term) {
                $query->whereHas('user', fn ($user) => $user->where('mobile', 'like', "%{$term}%"))
                    ->orWhereHas('package', fn ($package) => $package->where('name', 'like', "%{$term}%"));
            }))
            ->latest('id')
            ->paginate(20);

        return view('package::livewire.admin.gifted-package', compact('packages'));
    }
}
