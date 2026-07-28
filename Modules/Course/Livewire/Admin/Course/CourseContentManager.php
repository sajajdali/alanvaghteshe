<?php

namespace Modules\Course\Livewire\Admin\Course;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Course\app\Models\Course;
use Modules\Course\app\Models\CourseLesson;
use Modules\Course\app\Models\CourseSection;

class CourseContentManager extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public Course $course;
    public ?int $editingSectionId = null;
    public ?int $editingLessonId = null;
    public ?int $lessonSectionId = null;
    public $lessonVideoFile = null;
    public $lessonDocumentFile = null;
    public ?string $uploadedLessonVideoUrl = null;
    public ?string $uploadedLessonVideoName = null;
    public ?string $uploadedLessonDocumentUrl = null;
    public ?string $uploadedLessonDocumentName = null;
    protected string $videoPath = 'course/lessons';
    protected string $documentPath = 'course/documents';

    public array $sectionForm = [
        'title' => '',
        'description' => '',
        'sort_order' => 1,
        'is_active' => true,
    ];

    public array $lessonForm = [
        'course_section_id' => null,
        'title' => '',
        'description' => '',
        'content_type' => 'video',
        'video_url' => '',
        'document_url' => '',
        'duration_seconds' => 0,
        'is_preview' => false,
        'sort_order' => 1,
        'is_active' => true,
    ];

    public function mount(Course $course): void
    {
        $this->authorize('update', $course);
        $this->course = $course;
        $this->resetSectionForm();
    }

    public function saveSection(): void
    {
        $validated = $this->validate($this->sectionRules(), [], $this->sectionAttributes())['sectionForm'];

        $section = $this->editingSectionId
            ? $this->course->sections()->findOrFail($this->editingSectionId)
            : new CourseSection(['course_id' => $this->course->id]);

        $section->fill($validated);
        $section->course()->associate($this->course);
        $section->save();

        $message = $this->editingSectionId
            ? 'بخش دوره با موفقیت ویرایش شد.'
            : 'بخش جدید با موفقیت اضافه شد.';

        $this->resetSectionForm();
        session()->flash('success', $message);
    }

    public function editSection(int $sectionId): void
    {
        $section = $this->course->sections()->findOrFail($sectionId);

        $this->editingSectionId = $section->id;
        $this->sectionForm = [
            'title' => $section->title,
            'description' => $section->description ?? '',
            'sort_order' => $section->sort_order,
            'is_active' => (bool) $section->is_active,
        ];
    }

    public function deleteSection(int $sectionId): void
    {
        $section = $this->course->sections()->with('lessons')->findOrFail($sectionId);

        foreach ($section->lessons as $lesson) {
            $this->deleteStoredPublicFile($lesson->video_url);
            $this->deleteStoredPublicFile($lesson->document_url);
        }

        $section->delete();

        if ($this->editingSectionId === $sectionId) {
            $this->resetSectionForm();
        }

        if ($this->lessonSectionId === $sectionId) {
            $this->resetLessonForm();
        }

        session()->flash('success', 'بخش دوره حذف شد.');
    }

    public function startCreateLesson(int $sectionId): void
    {
        $section = $this->course->sections()->findOrFail($sectionId);
        $this->resetLessonForm($section->id);
    }

    public function editLesson(int $lessonId): void
    {
        $lesson = CourseLesson::query()
            ->whereHas('section', fn ($query) => $query->where('course_id', $this->course->id))
            ->findOrFail($lessonId);

        $this->editingLessonId = $lesson->id;
        $this->lessonSectionId = $lesson->course_section_id;
        $this->lessonForm = [
            'course_section_id' => $lesson->course_section_id,
            'title' => $lesson->title,
            'description' => $lesson->description ?? '',
            'content_type' => $lesson->content_type ?: 'video',
            'video_url' => $lesson->video_url ?? '',
            'document_url' => $lesson->document_url ?? '',
            'duration_seconds' => $lesson->duration_seconds ?? 0,
            'is_preview' => (bool) $lesson->is_preview,
            'sort_order' => $lesson->sort_order,
            'is_active' => (bool) $lesson->is_active,
        ];
        $this->syncLessonVideoPreview($lesson->video_url);
        $this->syncLessonDocumentPreview($lesson->document_url);
    }

    public function saveLesson(): void
    {
        $validated = $this->validate($this->lessonRules(), [], $this->lessonAttributes())['lessonForm'];

        $section = $this->course->sections()->findOrFail((int) $validated['course_section_id']);

        $lesson = $this->editingLessonId
            ? $section->lessons()->findOrFail($this->editingLessonId)
            : new CourseLesson();
        $oldVideoUrl = $lesson->exists ? $lesson->video_url : null;
        $oldDocumentUrl = $lesson->exists ? $lesson->document_url : null;

        if ($validated['content_type'] !== 'video') {
            $this->deleteStoredPublicFile($oldVideoUrl);
            $validated['video_url'] = null;
            $validated['duration_seconds'] = 0;
            $validated['is_preview'] = false;
        } else {
            $validated['video_url'] = $this->prepareLessonVideoForSave($oldVideoUrl);
        }

        if ($validated['content_type'] !== 'document') {
            $this->deleteStoredPublicFile($oldDocumentUrl);
            $validated['document_url'] = null;
        } else {
            $validated['document_url'] = $this->prepareLessonDocumentForSave($oldDocumentUrl);
        }

        $lesson->fill($validated);
        $lesson->section()->associate($section);
        $lesson->save();

        $message = $this->editingLessonId
            ? 'زیر‌بخش با موفقیت ویرایش شد.'
            : 'زیر‌بخش جدید با موفقیت اضافه شد.';

        $this->resetLessonForm($section->id);
        session()->flash('success', $message);
    }

    public function deleteLesson(int $lessonId): void
    {
        $lesson = CourseLesson::query()
            ->whereHas('section', fn ($query) => $query->where('course_id', $this->course->id))
            ->findOrFail($lessonId);

        $sectionId = $lesson->course_section_id;
        $this->deleteStoredPublicFile($lesson->video_url);
        $this->deleteStoredPublicFile($lesson->document_url);
        $lesson->delete();

        if ($this->editingLessonId === $lessonId) {
            $this->resetLessonForm($sectionId);
        }

        session()->flash('success', 'زیر‌بخش حذف شد.');
    }


    public function cancelSectionEdit(): void
    {
        $this->resetSectionForm();
    }

    public function cancelLessonEdit(): void
    {
        $this->resetLessonForm($this->lessonSectionId);
    }

    public function updatedLessonFormContentType($value): void
    {
        if ($value !== 'video') {
            $this->lessonForm['video_url'] = '';
            $this->lessonForm['duration_seconds'] = 0;
            $this->lessonForm['is_preview'] = false;
            $this->removeTemporaryUploadedLessonVideo();
            $this->lessonVideoFile = null;
            $this->uploadedLessonVideoUrl = null;
            $this->uploadedLessonVideoName = null;
        }

        if ($value !== 'document') {
            $this->lessonForm['document_url'] = '';
            $this->removeTemporaryUploadedLessonDocument();
            $this->lessonDocumentFile = null;
            $this->uploadedLessonDocumentUrl = null;
            $this->uploadedLessonDocumentName = null;
        }
    }

    public function updatedLessonVideoFile(): void
    {
        $this->validate([
            'lessonVideoFile' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm|max:102400',
        ], [
            'lessonVideoFile.file' => 'فایل ویدئو معتبر نیست.',
            'lessonVideoFile.mimetypes' => 'فرمت ویدئو باید mp4، mov، avi، mkv یا webm باشد.',
            'lessonVideoFile.max' => 'حجم ویدئو نباید بیشتر از ۱۰۰ مگابایت باشد.',
        ]);

        $this->removeTemporaryUploadedLessonVideo();

        $temporaryPath = $this->lessonVideoFile->store('temp/' . $this->videoPath, ['disk' => 'public']);

        $this->lessonForm['video_url'] = $temporaryPath;
        $this->uploadedLessonVideoUrl = Storage::disk('public')->url($temporaryPath);
        $this->uploadedLessonVideoName = $this->lessonVideoFile->getClientOriginalName();
    }

    public function deleteLessonVideo(): void
    {
        $currentVideo = $this->lessonForm['video_url'] ?? null;
        $this->removeTemporaryUploadedLessonVideo();

        if ($currentVideo && ! Str::startsWith($currentVideo, 'temp/')) {
            $this->deleteStoredPublicFile($currentVideo);
        }

        $this->lessonForm['video_url'] = '';
        $this->lessonVideoFile = null;
        $this->uploadedLessonVideoUrl = null;
        $this->uploadedLessonVideoName = null;
    }

    public function updatedLessonDocumentFile(): void
    {
        $this->validate([
            'lessonDocumentFile' => 'nullable|file|max:51200',
        ], [
            'lessonDocumentFile.file' => 'فایل داکیومنت معتبر نیست.',
            'lessonDocumentFile.max' => 'حجم داکیومنت نباید بیشتر از ۵۰ مگابایت باشد.',
        ]);

        $this->removeTemporaryUploadedLessonDocument();

        $temporaryPath = $this->lessonDocumentFile->store('temp/' . $this->documentPath, ['disk' => 'public']);

        $this->lessonForm['document_url'] = $temporaryPath;
        $this->uploadedLessonDocumentUrl = Storage::disk('public')->url($temporaryPath);
        $this->uploadedLessonDocumentName = $this->lessonDocumentFile->getClientOriginalName();
    }

    public function deleteLessonDocument(): void
    {
        $currentDocument = $this->lessonForm['document_url'] ?? null;
        $this->removeTemporaryUploadedLessonDocument();

        if ($currentDocument && ! Str::startsWith($currentDocument, 'temp/')) {
            $this->deleteStoredPublicFile($currentDocument);
        }

        $this->lessonForm['document_url'] = '';
        $this->lessonDocumentFile = null;
        $this->uploadedLessonDocumentUrl = null;
        $this->uploadedLessonDocumentName = null;
    }

    private function resetSectionForm(): void
    {
        $this->editingSectionId = null;
        $this->sectionForm = [
            'title' => '',
            'description' => '',
            'sort_order' => (int) $this->course->sections()->max('sort_order') + 1,
            'is_active' => true,
        ];
        $this->resetErrorBag();
    }

    private function resetLessonForm(?int $sectionId = null): void
    {
        $this->editingLessonId = null;
        $this->lessonSectionId = $sectionId;

        $nextSortOrder = 1;
        if ($sectionId) {
            $nextSortOrder = (int) $this->course->sections()
                ->find($sectionId)?->lessons()
                ->max('sort_order') + 1;
        }

        $this->lessonForm = [
            'course_section_id' => $sectionId,
            'title' => '',
            'description' => '',
            'content_type' => 'video',
            'video_url' => '',
            'document_url' => '',
            'duration_seconds' => 0,
            'is_preview' => false,
            'sort_order' => $nextSortOrder,
            'is_active' => true,
        ];
        $this->lessonVideoFile = null;
        $this->lessonDocumentFile = null;
        $this->uploadedLessonVideoUrl = null;
        $this->uploadedLessonVideoName = null;
        $this->uploadedLessonDocumentUrl = null;
        $this->uploadedLessonDocumentName = null;
        $this->resetErrorBag();
    }

    private function sectionRules(): array
    {
        return [
            'sectionForm.title' => ['required', 'string', 'max:255'],
            'sectionForm.description' => ['nullable', 'string'],
            'sectionForm.sort_order' => ['required', 'integer', 'min:1'],
            'sectionForm.is_active' => ['required', 'boolean'],
        ];
    }

    private function lessonRules(): array
    {
        return [
            'lessonForm.course_section_id' => [
                'required',
                Rule::exists('course_sections', 'id')->where(fn ($query) => $query->where('course_id', $this->course->id)),
            ],
            'lessonForm.title' => ['required', 'string', 'max:255'],
            'lessonForm.description' => ['nullable', 'string'],
            'lessonForm.content_type' => ['required', Rule::in(['video', 'document', 'text'])],
            'lessonForm.video_url' => ['nullable', 'required_if:lessonForm.content_type,video', 'string', 'max:2048'],
            'lessonForm.document_url' => ['nullable', 'required_if:lessonForm.content_type,document', 'string', 'max:2048'],
            'lessonForm.duration_seconds' => ['nullable', 'integer', 'min:0'],
            'lessonForm.is_preview' => ['required', 'boolean'],
            'lessonForm.sort_order' => ['required', 'integer', 'min:1'],
            'lessonForm.is_active' => ['required', 'boolean'],
        ];
    }


    private function prepareLessonVideoForSave(?string $oldVideoUrl = null): ?string
    {
        $videoValue = $this->lessonForm['video_url'] ?? null;

        if (blank($videoValue)) {
            if ($oldVideoUrl) {
                $this->deleteStoredPublicFile($oldVideoUrl);
            }

            return null;
        }

        if (! Str::startsWith($videoValue, 'temp/')) {
            return $videoValue;
        }

        $finalPath = str_replace('temp/', '', $videoValue);
        Storage::disk('public')->move($videoValue, $finalPath);

        if ($oldVideoUrl) {
            $this->deleteStoredPublicFile($oldVideoUrl, $finalPath);
        }

        $videoUrl = Storage::disk('public')->url($finalPath);
        $this->lessonForm['video_url'] = $videoUrl;
        $this->uploadedLessonVideoUrl = $videoUrl;

        return $videoUrl;
    }

    private function prepareLessonDocumentForSave(?string $oldDocumentUrl = null): ?string
    {
        $documentValue = $this->lessonForm['document_url'] ?? null;

        if (blank($documentValue)) {
            if ($oldDocumentUrl) {
                $this->deleteStoredPublicFile($oldDocumentUrl);
            }

            return null;
        }

        if (! Str::startsWith($documentValue, 'temp/')) {
            return $documentValue;
        }

        $finalPath = str_replace('temp/', '', $documentValue);
        Storage::disk('public')->move($documentValue, $finalPath);

        if ($oldDocumentUrl) {
            $this->deleteStoredPublicFile($oldDocumentUrl, $finalPath);
        }

        $documentUrl = Storage::disk('public')->url($finalPath);
        $this->lessonForm['document_url'] = $documentUrl;
        $this->uploadedLessonDocumentUrl = $documentUrl;

        return $documentUrl;
    }

    private function syncLessonVideoPreview(?string $videoUrl): void
    {
        if (blank($videoUrl)) {
            $this->uploadedLessonVideoUrl = null;
            $this->uploadedLessonVideoName = null;
            return;
        }

        $this->uploadedLessonVideoUrl = Str::startsWith($videoUrl, 'temp/')
            ? Storage::disk('public')->url($videoUrl)
            : $videoUrl;
        $this->uploadedLessonVideoName = basename(parse_url($this->uploadedLessonVideoUrl, PHP_URL_PATH) ?: $this->uploadedLessonVideoUrl);
    }

    private function syncLessonDocumentPreview(?string $documentUrl): void
    {
        if (blank($documentUrl)) {
            $this->uploadedLessonDocumentUrl = null;
            $this->uploadedLessonDocumentName = null;
            return;
        }

        $this->uploadedLessonDocumentUrl = Str::startsWith($documentUrl, 'temp/')
            ? Storage::disk('public')->url($documentUrl)
            : $documentUrl;
        $this->uploadedLessonDocumentName = basename(parse_url($this->uploadedLessonDocumentUrl, PHP_URL_PATH) ?: $this->uploadedLessonDocumentUrl);
    }

    private function removeTemporaryUploadedLessonVideo(): void
    {
        $currentVideo = $this->lessonForm['video_url'] ?? null;

        if ($currentVideo && Str::startsWith($currentVideo, 'temp/')) {
            Storage::disk('public')->delete($currentVideo);
        }
    }

    private function removeTemporaryUploadedLessonDocument(): void
    {
        $currentDocument = $this->lessonForm['document_url'] ?? null;

        if ($currentDocument && Str::startsWith($currentDocument, 'temp/')) {
            Storage::disk('public')->delete($currentDocument);
        }
    }

    private function deleteStoredPublicFile(?string $fileUrl, ?string $exceptRelativePath = null): void
    {
        if (blank($fileUrl)) {
            return;
        }

        $relativePath = $this->extractPublicRelativePath($fileUrl);

        if (! $relativePath || $relativePath === $exceptRelativePath) {
            return;
        }

        Storage::disk('public')->delete($relativePath);
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

    private function sectionAttributes(): array
    {
        return [
            'sectionForm.title' => 'عنوان بخش',
            'sectionForm.description' => 'توضیحات بخش',
            'sectionForm.sort_order' => 'ترتیب نمایش بخش',
            'sectionForm.is_active' => 'وضعیت بخش',
        ];
    }

    private function lessonAttributes(): array
    {
        return [
            'lessonForm.course_section_id' => 'بخش انتخاب‌شده',
            'lessonForm.title' => 'عنوان زیر‌بخش',
            'lessonForm.description' => 'توضیحات زیر‌بخش',
            'lessonForm.content_type' => 'نوع محتوا',
            'lessonForm.video_url' => 'ویدئو',
            'lessonForm.document_url' => 'داکیومنت',
            'lessonForm.duration_seconds' => 'مدت ویدئو',
            'lessonForm.is_preview' => 'پیش‌نمایش',
            'lessonForm.sort_order' => 'ترتیب نمایش زیر‌بخش',
            'lessonForm.is_active' => 'وضعیت زیر‌بخش',
        ];
    }

    public function render()
    {
        $course = $this->course->fresh()->load(['sections.lessons']);

        return view('course::livewire.admin.course.course-content-manager', [
            'course' => $course,
            'contentTypeLabels' => [
                'video' => 'ویدئو',
                'document' => 'داکیومنت',
                'text' => 'فقط توضیحات',
            ],
        ])->title('مدیریت بخش‌ها و زیر‌بخش‌های دوره');
    }
}
