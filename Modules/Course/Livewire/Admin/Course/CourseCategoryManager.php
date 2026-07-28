<?php

namespace Modules\Course\Livewire\Admin\Course;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Course\app\Models\CourseCategory;

#[Title('گروه‌بندی دوره‌ها')]
class CourseCategoryManager extends Component
{
    use WithFileUploads;

    public ?CourseCategory $editingCategory = null;

    public string $title = '';
    public string $slug = '';
    public ?string $image = '';
    public ?string $parent_id = '';
    public string $sort_order = '1';
    public bool $is_active = true;
    public $photo = null;
    public ?string $uploadedPhotoUrl = null;
    public ?string $uploadedFileName = null;
    public ?string $uploadedFileType = null;

    protected function rules(): array
    {
        $categoryId = $this->editingCategory?->id;

        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:course_categories,slug,' . $categoryId,
            'image' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:course_categories,id',
            'sort_order' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ];
    }

    protected $messages = [
        'title.required' => 'عنوان گروه‌بندی را وارد کنید',
        'slug.required' => 'اسلاگ گروه‌بندی را وارد کنید',
        'slug.unique' => 'این اسلاگ قبلاً ثبت شده است',
        'image.max' => 'مسیر تصویر معتبر نیست',
        'parent_id.exists' => 'دسته والد معتبر نیست',
        'sort_order.required' => 'ترتیب را وارد کنید',
    ];

    public function updatedTitle($value): void
    {
        if (blank($this->slug)) {
            $this->slug = Str::slug($value, '-');
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

        $path = $this->photo->store('temp/course/category', ['disk' => 'public']);

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

    public function edit(int $categoryId): void
    {
        $category = CourseCategory::query()->findOrFail($categoryId);

        $this->editingCategory = $category;
        $this->title = $category->title;
        $this->slug = $category->slug;
        $this->image = $category->image ?? '';
        $this->parent_id = $category->parent_id ? (string) $category->parent_id : '';
        $this->sort_order = (string) $category->sort_order;
        $this->is_active = (bool) $category->is_active;
        $this->syncPreview($category->image);
    }

    public function save()
    {
        if (blank($this->slug)) {
            $this->slug = Str::slug($this->title, '-');
        }

        $this->validate();

        $parent = filled($this->parent_id)
            ? CourseCategory::query()->with('parent')->findOrFail((int) $this->parent_id)
            : null;

        if ($parent && $parent->parent_id) {
            $this->addError('parent_id', 'فقط تا ۲ سطح گروه‌بندی مجاز است.');
            return;
        }

        if ($this->editingCategory && filled($this->parent_id) && (int) $this->parent_id === $this->editingCategory->id) {
            $this->addError('parent_id', 'یک دسته نمی‌تواند والد خودش باشد.');
            return;
        }

        $payload = [
            'title' => $this->title,
            'slug' => Str::slug($this->slug, '-'),
            'image' => $this->prepareImageForSave(),
            'parent_id' => filled($this->parent_id) ? (int) $this->parent_id : null,
            'sort_order' => (int) $this->sort_order,
            'is_active' => $this->is_active,
        ];

        if ($this->editingCategory) {
            $this->editingCategory->update($payload);
            $message = 'گروه‌بندی با موفقیت ویرایش شد.';
        } else {
            CourseCategory::create($payload);
            $message = 'گروه‌بندی با موفقیت ثبت شد.';
        }

        $this->resetForm();
        session()->flash('success', $message);
    }

    public function delete(int $categoryId): void
    {
        $category = CourseCategory::query()->withCount(['children', 'courses'])->findOrFail($categoryId);

        if ($category->children_count > 0 || $category->courses_count > 0) {
            session()->flash('error', 'این گروه‌بندی زیرشاخه یا دوره دارد و قابل حذف نیست.');
            return;
        }

        if ($category->image) {
            $relativePath = $this->extractRelativePath($category->image);
            if ($relativePath) {
                Storage::disk('public')->delete($relativePath);
            }
        }

        $category->delete();
        session()->flash('success', 'گروه‌بندی حذف شد.');
    }

    public function resetForm(): void
    {
        $this->editingCategory = null;
        $this->title = '';
        $this->slug = '';
        $this->image = '';
        $this->parent_id = '';
        $this->sort_order = '1';
        $this->is_active = true;
        $this->photo = null;
        $this->uploadedPhotoUrl = null;
        $this->uploadedFileName = null;
        $this->uploadedFileType = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('course::livewire.admin.course.course-category-manager', [
            'categories' => CourseCategory::query()
                ->with(['parent', 'children'])
                ->withCount('courses')
                ->orderBy('parent_id')
                ->orderBy('sort_order')
                ->get(),
            'parentOptions' => CourseCategory::query()
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    private function syncPreview(?string $image): void
    {
        if (! filled($image)) {
            $this->uploadedPhotoUrl = null;
            $this->uploadedFileName = null;
            $this->uploadedFileType = null;
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

        $oldImage = $this->editingCategory?->image;
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
