<?php

namespace Modules\Course\Livewire\Admin\Course;

use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\Course\app\Models\CourseUser;

#[Title('جزئیات مشاهده دوره')]
class CoursePurchaseDetail extends Component
{
    public CourseUser $purchase;

    public function mount(CourseUser $purchase): void
    {
        $this->purchase = $purchase->load([
            'user',
            'course.sections.lessons',
            'progress.lesson',
        ]);
    }

    public function render()
    {
        $progressByLessonId = $this->purchase->progress
            ->keyBy('course_lesson_id');

        $sections = $this->purchase->course?->sections
            ?->map(function ($section) use ($progressByLessonId) {
                $lessons = $section->lessons->map(function ($lesson) use ($progressByLessonId) {
                    $progress = $progressByLessonId->get($lesson->id);

                    return [
                        'lesson' => $lesson,
                        'progress' => $progress,
                        'is_seen' => $progress !== null,
                    ];
                });

                return [
                    'section' => $section,
                    'lessons' => $lessons,
                    'seen_count' => $lessons->where('is_seen', true)->count(),
                    'total_count' => $lessons->count(),
                ];
            }) ?? collect();

        return view('course::livewire.admin.course.course-purchase-detail', [
            'sections' => $sections,
            'seenLessonsCount' => $progressByLessonId->count(),
            'totalLessonsCount' => (int) ($this->purchase->course?->lessons()->count() ?? 0),
        ]);
    }
}
