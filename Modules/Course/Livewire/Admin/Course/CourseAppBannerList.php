<?php

namespace Modules\Course\Livewire\Admin\Course;

use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Course\app\Models\CourseAppBanner;

#[Title('بنرهای اپلیکیشن')]
class CourseAppBannerList extends Component
{
    use WithPagination;

    public string $searchPanel = '';

    #[Url]
    public array $search = [];

    public function startSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $bannerId)
    {
        $banner = CourseAppBanner::query()->findOrFail($bannerId);

        if ($banner->image) {
            $relativePath = str_replace('/storage/', '', parse_url($banner->image, PHP_URL_PATH) ?: $banner->image);
            \Storage::disk('public')->delete($relativePath);
        }

        $banner->delete();

        session()->flash('success', 'بنر با موفقیت حذف شد.');
    }

    public function render()
    {
        $banners = CourseAppBanner::query()
            ->with('course')
            ->when(isset($this->search['title']) && filled($this->search['title']), fn ($query) => $query->where('title', 'like', '%' . $this->search['title'] . '%'))
            ->when(isset($this->search['position']) && filled($this->search['position']), fn ($query) => $query->where('position', $this->search['position']))
            ->when(isset($this->search['course']) && filled($this->search['course']), fn ($query) => $query->whereHas('course', fn ($q) => $q->where('title', 'like', '%' . $this->search['course'] . '%')))
            ->when(isset($this->search['is_active']) && $this->search['is_active'] !== '', fn ($query) => $query->where('is_active', (int) $this->search['is_active']))
            ->orderBy('position')
            ->orderBy('priority')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('course::livewire.admin.course.course-app-banner-list', [
            'banners' => $banners,
            'positions' => CourseAppBanner::positions(),
        ]);
    }
}
