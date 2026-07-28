<?php

namespace Modules\Course\Livewire\Admin\Course;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Hekmatinasser\Verta\Facades\Verta;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Course\app\Models\Course;
use Modules\Course\app\Models\CourseCategory;

class CourseCreateOrUpdate extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public ?Course $course = null;
    protected string $filePath = 'course';
    public string $title = '';
    public string $slug = '';
    public ?string $short_description = '';
    public ?string $description = '';
    public ?string $thumbnail = '';
    public string $price = '0';
    public ?string $discounted_price = '';
    public string $final_price = '0';
    public string $duration_minutes = '0';
    public string $access_days = '0';
    public ?string $category_id = '';
    public string $level = '1';
    public ?string $capacity = '';
    public string $sort_order = '1';
    public bool $is_latest = false;
    public ?string $latest_sort_order = '';
    public bool $is_popular = false;
    public ?string $popular_sort_order = '';
    public bool $is_active = true;
    public bool $is_published = false;
    public bool $is_purchasable = true;
    public ?string $published_at = '';
    public bool $isEdited = false;
    public $photo = null;
    public ?string $uploadedPhotoUrl = null;
    public ?string $uploadedFileName = null;
    public ?string $uploadedFileType = null;

    protected function rules(): array
    {
        $courseId = $this->course?->id;

        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:courses,slug,' . $courseId,
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string|max:255',
            'price' => 'required|integer|min:0',
            'discounted_price' => 'nullable|integer|min:0|lte:price',
            'final_price' => 'required|integer|min:0',
            'duration_minutes' => 'required|integer|min:0',
            'access_days' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:course_categories,id',
            'level' => 'required|in:' . implode(',', array_keys(Course::levels())),
            'capacity' => 'nullable|integer|min:1',
            'sort_order' => 'required|integer|min:1',
            'is_latest' => 'required|boolean',
            'latest_sort_order' => 'nullable|required_if:is_latest,true|integer|min:1',
            'is_popular' => 'required|boolean',
            'popular_sort_order' => 'nullable|required_if:is_popular,true|integer|min:1',
            'is_active' => 'required|boolean',
            'is_published' => 'required|boolean',
            'is_purchasable' => 'required|boolean',
            'published_at' => 'nullable|string',
        ];
    }

    protected $messages = [
        'title.required' => 'عنوان دوره را وارد کنید',
        'slug.required' => 'اسلاگ دوره را وارد کنید',
        'slug.unique' => 'این اسلاگ قبلاً ثبت شده است',
        'short_description.max' => 'توضیح کوتاه حداکثر ۵۰۰ کاراکتر می‌تواند باشد',
        'thumbnail.max' => 'آدرس تصویر بیش از حد مجاز است',
        'price.required' => 'قیمت دوره را وارد کنید',
        'price.integer' => 'قیمت دوره باید عددی باشد',
        'price.min' => 'قیمت دوره نمی‌تواند منفی باشد',
        'discounted_price.integer' => 'قیمت پس از تخفیف باید عددی باشد',
        'discounted_price.min' => 'قیمت پس از تخفیف نمی‌تواند منفی باشد',
        'discounted_price.lte' => 'قیمت پس از تخفیف نباید بیشتر از قیمت اصلی باشد',
        'final_price.required' => 'قیمت نهایی معتبر نیست',
        'duration_minutes.required' => 'مدت دوره را وارد کنید',
        'duration_minutes.integer' => 'مدت دوره باید عددی باشد',
        'duration_minutes.min' => 'مدت دوره نمی‌تواند منفی باشد',
        'access_days.required' => 'مدت دسترسی دوره را وارد کنید',
        'access_days.integer' => 'مدت دسترسی دوره باید عددی باشد',
        'access_days.min' => 'مدت دسترسی دوره نمی‌تواند منفی باشد',
        'category_id.exists' => 'گروه‌بندی انتخاب‌شده معتبر نیست',
        'level.required' => 'سطح دوره را انتخاب کنید',
        'level.in' => 'سطح دوره معتبر نیست',
        'capacity.integer' => 'ظرفیت دوره باید عددی باشد',
        'capacity.min' => 'ظرفیت دوره حداقل باید ۱ باشد',
        'sort_order.required' => 'اولویت نمایش را وارد کنید',
        'sort_order.integer' => 'اولویت نمایش باید عددی باشد',
        'sort_order.min' => 'اولویت نمایش حداقل باید ۱ باشد',
        'latest_sort_order.required_if' => 'اولویت جدیدترین را وارد کنید',
        'latest_sort_order.integer' => 'اولویت جدیدترین باید عددی باشد',
        'latest_sort_order.min' => 'اولویت جدیدترین حداقل باید ۱ باشد',
        'popular_sort_order.required_if' => 'اولویت محبوب‌ترین را وارد کنید',
        'popular_sort_order.integer' => 'اولویت محبوب‌ترین باید عددی باشد',
        'popular_sort_order.min' => 'اولویت محبوب‌ترین حداقل باید ۱ باشد',
        'published_at.date' => 'تاریخ انتشار معتبر نیست',
    ];

    public function updatedPhoto(): void
    {
        $this->validate([
            'photo' => 'nullable|image|max:5120',
        ], [
            'photo.image' => 'فایل انتخاب‌شده باید تصویر باشد',
            'photo.max' => 'حجم تصویر نباید بیشتر از ۵ مگابایت باشد',
        ]);

        $path = $this->photo->store('temp/' . $this->filePath, ['disk' => 'public']);

        $this->thumbnail = $path;
        $this->uploadedPhotoUrl = Storage::disk('public')->url($path);
        $this->uploadedFileName = $this->photo->getClientOriginalName();
        $this->uploadedFileType = $this->photo->getMimeType();
    }

    public function deletePhoto(): void
    {
        if (filled($this->thumbnail)) {
            $relativePath = $this->extractPublicRelativePath($this->thumbnail);

            if ($relativePath) {
                Storage::disk('public')->delete($relativePath);
            }
        }

        $this->photo = null;
        $this->thumbnail = '';
        $this->uploadedPhotoUrl = null;
        $this->uploadedFileName = null;
        $this->uploadedFileType = null;
    }

    public function mount(): void
    {
        $course = request()->route('course');

        if ($course instanceof Course) {
            $this->authorize('update', $course);
            $this->isEdited = true;
            $this->course = $course;
            $this->title = $course->title ?? '';
            $this->slug = $course->slug ?? '';
            $this->short_description = $course->short_description ?? '';
            $this->description = $course->description ?? '';
            $this->thumbnail = $course->thumbnail ?? '';
            $this->price = (string) ($course->price ?? 0);
            $this->discounted_price = isset($course->discounted_price) ? (string) $course->discounted_price : '';
            $this->final_price = (string) ($course->final_price ?? $course->price ?? 0);
            $this->duration_minutes = (string) ($course->duration_minutes ?? 0);
            $this->access_days = (string) ($course->access_days ?? 0);
            $this->category_id = isset($course->category_id) ? (string) $course->category_id : '';
            $this->level = (string) ($course->level ?? Course::LEVEL_EASY);
            $this->capacity = isset($course->capacity) ? (string) $course->capacity : '';
            $this->sort_order = (string) ($course->sort_order ?? 1);
            $this->is_latest = (bool) $course->is_latest;
            $this->latest_sort_order = isset($course->latest_sort_order) ? (string) $course->latest_sort_order : '';
            $this->is_popular = (bool) $course->is_popular;
            $this->popular_sort_order = isset($course->popular_sort_order) ? (string) $course->popular_sort_order : '';
            $this->is_active = (bool) $course->is_active;
            $this->is_published = (bool) $course->is_published;
            $this->is_purchasable = (bool) $course->is_purchasable;
            $this->published_at = $course->published_at ? verta($course->published_at)->format('Y/m/d') : '';
            $this->syncThumbnailPreview($course->thumbnail);

            return;
        }

        $this->authorize('create', Course::class);
        $this->sort_order = (string) Course::maxOrder();
    }

    public function updatedIsLatest(): void
    {
        if (! $this->is_latest) {
            $this->latest_sort_order = '';
        }
    }

    public function updatedIsPopular(): void
    {
        if (! $this->is_popular) {
            $this->popular_sort_order = '';
        }
    }

    public function updatedTitle($value): void
    {
        if (! $this->isEdited && blank($this->slug)) {
            $this->slug = Str::slug($value, '-');
        }
    }

    public function updateOrCreate()
    {
        if (blank($this->slug)) {
            $this->slug = Str::slug($this->title, '-');
        }

        $this->syncFinalPrice();

        $this->validate();

        if (filled($this->published_at)) {
            try {
                Verta::parse($this->published_at);
            } catch (\Throwable $e) {
                $this->addError('published_at', 'تاریخ انتشار معتبر نیست');
                return null;
            }
        }

        $publishedAt = $this->is_published
            ? ($this->published_at ? Verta::parse($this->published_at)->toCarbon()->startOfDay() : now())
            : null;

        $thumbnailPath = $this->prepareThumbnailForSave();

        $payload = [
            'title' => $this->title,
            'slug' => Str::slug($this->slug, '-'),
            'short_description' => $this->short_description ?: null,
            'description' => $this->description ?: null,
            'thumbnail' => $thumbnailPath ?: null,
            'price' => (int) $this->price,
            'discounted_price' => filled($this->discounted_price) ? (int) $this->discounted_price : null,
            'final_price' => (int) $this->final_price,
            'duration_minutes' => (int) $this->duration_minutes,
            'access_days' => (int) $this->access_days,
            'category_id' => filled($this->category_id) ? (int) $this->category_id : null,
            'level' => (int) $this->level,
            'capacity' => filled($this->capacity) ? (int) $this->capacity : null,
            'sort_order' => (int) $this->sort_order,
            'is_latest' => $this->is_latest,
            'latest_sort_order' => $this->is_latest && filled($this->latest_sort_order) ? (int) $this->latest_sort_order : null,
            'is_popular' => $this->is_popular,
            'popular_sort_order' => $this->is_popular && filled($this->popular_sort_order) ? (int) $this->popular_sort_order : null,
            'is_active' => $this->is_active,
            'is_published' => $this->is_published,
            'is_purchasable' => $this->is_purchasable,
            'published_at' => $publishedAt,
        ];

        if ($this->isEdited) {
            $this->course->update($payload);
            $message = 'دوره با موفقیت ویرایش شد';
        } else {
            $this->course = Course::create($payload);
            $message = 'دوره با موفقیت اضافه شد';
        }

        return redirect()->route('admin.course.index')->with('success', $message);
    }

    public function updatedPrice(): void
    {
        $this->syncFinalPrice();
    }

    public function updatedDiscountedPrice(): void
    {
        $this->syncFinalPrice();
    }

    private function syncFinalPrice(): void
    {
        $price = (int) ($this->price ?: 0);
        $discountedPrice = filled($this->discounted_price) ? (int) $this->discounted_price : null;
        $this->final_price = (string) ($discountedPrice ?? $price);
    }

    private function prepareThumbnailForSave(): ?string
    {
        if (blank($this->thumbnail)) {
            return null;
        }

        if (! Str::startsWith($this->thumbnail, 'temp/')) {
            return $this->thumbnail;
        }

        $oldThumbnail = $this->isEdited ? $this->course?->thumbnail : null;
        $finalPath = str_replace('temp/', '', $this->thumbnail);

        Storage::disk('public')->move($this->thumbnail, $finalPath);

        if ($oldThumbnail) {
            $oldRelativePath = $this->extractPublicRelativePath($oldThumbnail);
            if ($oldRelativePath && $oldRelativePath !== $finalPath) {
                Storage::disk('public')->delete($oldRelativePath);
            }
        }

        $thumbnailUrl = Storage::disk('public')->url($finalPath);
        $this->thumbnail = $thumbnailUrl;
        $this->uploadedPhotoUrl = $thumbnailUrl;

        return $thumbnailUrl;
    }

    private function syncThumbnailPreview(?string $thumbnail): void
    {
        if (blank($thumbnail)) {
            $this->uploadedPhotoUrl = null;
            $this->uploadedFileName = null;
            $this->uploadedFileType = null;
            return;
        }

        $this->uploadedPhotoUrl = $thumbnail;
        $this->uploadedFileName = basename(parse_url($thumbnail, PHP_URL_PATH) ?: $thumbnail);
        $extension = strtolower(pathinfo($this->uploadedFileName, PATHINFO_EXTENSION));
        $this->uploadedFileType = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'], true)
            ? 'image/' . ($extension === 'jpg' ? 'jpeg' : $extension)
            : 'application/octet-stream';
    }

    private function extractPublicRelativePath(string $path): ?string
    {
        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://')) {
            $storageUrl = Storage::disk('public')->url('');

            if (! Str::startsWith($path, $storageUrl)) {
                return null;
            }

            return ltrim(Str::after($path, $storageUrl), '/');
        }

        return ltrim($path, '/');
    }

    public function render()
    {
        $title = (! is_null($this->course) && $this->course->exists) ? 'ویرایش دوره' : 'ایجاد دوره جدید';

        return view('course::livewire.admin.course.course-create-or-update', [
            'categories' => CourseCategory::query()->with('parent')->where('is_active', true)->orderBy('parent_id')->orderBy('sort_order')->get(),
            'levels' => Course::levels(),
        ])->title($title);
    }
}
