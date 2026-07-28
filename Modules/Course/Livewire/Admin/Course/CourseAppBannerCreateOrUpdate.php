<?php

namespace Modules\Course\Livewire\Admin\Course;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Course\app\Models\Course;
use Modules\Course\app\Models\CourseAppBanner;

class CourseAppBannerCreateOrUpdate extends Component
{
    use WithFileUploads;

    public ?CourseAppBanner $banner = null;
    public bool $isEdited = false;

    public string $title = '';
    public ?int $course_id = null;
    public string $position = CourseAppBanner::POSITION_TOP;
    public string $sort_order = '1';
    public string $priority = '1';
    public bool $is_active = true;
    public ?string $image = '';

    public $photo = null;
    public ?string $uploadedPhotoUrl = null;
    public ?string $uploadedFileName = null;
    public ?string $uploadedFileType = null;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'position' => 'required|in:' . implode(',', array_keys(CourseAppBanner::positions())),
            'sort_order' => 'required|integer|min:1',
            'priority' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
            'image' => 'required|string|max:255',
        ];
    }

    protected $messages = [
        'title.required' => 'نام بنر را وارد کنید',
        'course_id.required' => 'دوره را انتخاب کنید',
        'course_id.exists' => 'دوره انتخاب‌شده معتبر نیست',
        'position.required' => 'جایگاه بنر را انتخاب کنید',
        'position.in' => 'جایگاه بنر معتبر نیست',
        'sort_order.required' => 'ترتیب بنر را وارد کنید',
        'priority.required' => 'اولویت بنر را وارد کنید',
        'image.required' => 'تصویر بنر را انتخاب کنید',
    ];

    public function mount(): void
    {
        $banner = request()->route('banner');

        if ($banner instanceof CourseAppBanner) {
            $this->isEdited = true;
            $this->banner = $banner;
            $this->title = $banner->title;
            $this->course_id = $banner->course_id;
            $this->position = $banner->position;
            $this->sort_order = (string) $banner->sort_order;
            $this->priority = (string) $banner->priority;
            $this->is_active = (bool) $banner->is_active;
            $this->image = $banner->image;
            $this->syncPreview($banner->image);
        }
    }

    public function updatedPhoto(): void
    {
        $this->validate([
            'photo' => 'nullable|image|max:5120',
        ], [
            'photo.image' => 'فایل انتخاب‌شده باید تصویر باشد',
            'photo.max' => 'حجم تصویر نباید بیشتر از ۵ مگابایت باشد',
        ]);

        $path = $this->photo->store('temp/course/banner', ['disk' => 'public']);

        $this->image = $path;
        $this->uploadedPhotoUrl = Storage::disk('public')->url($path);
        $this->uploadedFileName = $this->photo->getClientOriginalName();
        $this->uploadedFileType = $this->photo->getMimeType();
    }

    public function deletePhoto(): void
    {
        if (filled($this->image)) {
            $relativePath = $this->extractRelativePath($this->image);
            if ($relativePath) {
                Storage::disk('public')->delete($relativePath);
            }
        }

        $this->photo = null;
        $this->image = '';
        $this->uploadedPhotoUrl = null;
        $this->uploadedFileName = null;
        $this->uploadedFileType = null;
    }

    public function updateOrCreate()
    {
        $this->validate();

        $imagePath = $this->prepareImageForSave();

        $payload = [
            'title' => $this->title,
            'course_id' => $this->course_id,
            'position' => $this->position,
            'sort_order' => (int) $this->sort_order,
            'priority' => (int) $this->priority,
            'is_active' => $this->is_active,
            'image' => $imagePath,
        ];

        if ($this->isEdited) {
            $this->banner->update($payload);
            $message = 'بنر با موفقیت ویرایش شد.';
        } else {
            CourseAppBanner::create($payload);
            $message = 'بنر با موفقیت ایجاد شد.';
        }

        return redirect()->route('admin.course.banners.index')->with('success', $message);
    }

    public function render()
    {
        return view('course::livewire.admin.course.course-app-banner-create-or-update', [
            'courses' => Course::query()->orderBy('title')->get(['id', 'title']),
            'positions' => CourseAppBanner::positions(),
        ]);
    }

    private function syncPreview(?string $image): void
    {
        if (! filled($image)) {
            return;
        }

        $this->uploadedPhotoUrl = $image;
        $this->uploadedFileName = basename(parse_url($image, PHP_URL_PATH) ?: $image);
        $this->uploadedFileType = 'image/*';
    }

    private function prepareImageForSave(): ?string
    {
        if (blank($this->image)) {
            return null;
        }

        if (! str_starts_with($this->image, 'temp/')) {
            return $this->image;
        }

        $oldImage = $this->isEdited ? $this->banner?->image : null;
        $finalPath = str_replace('temp/', '', $this->image);
        Storage::disk('public')->move($this->image, $finalPath);

        if ($oldImage) {
            $oldRelativePath = $this->extractRelativePath($oldImage);
            if ($oldRelativePath && $oldRelativePath !== $finalPath) {
                Storage::disk('public')->delete($oldRelativePath);
            }
        }

        return Storage::disk('public')->url($finalPath);
    }

    private function extractRelativePath(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        $parsedPath = parse_url($path, PHP_URL_PATH);
        $path = $parsedPath ?: $path;

        return str_starts_with($path, '/storage/') ? substr($path, 9) : ltrim($path, '/');
    }
}
