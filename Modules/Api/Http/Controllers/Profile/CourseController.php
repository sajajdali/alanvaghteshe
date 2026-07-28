<?php

namespace Modules\Api\Http\Controllers\Profile;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\Api\Transformers\FaqResource;
use Modules\Api\app\Resources\Api\Course\CourseAppBannerResource;
use Modules\Api\app\Resources\Api\Course\CourseCommentPaginateResource;
use Modules\Api\app\Resources\Api\Course\CourseCommentResource;
use Modules\Api\app\Resources\Api\Course\CourseCategoryTreeResource;
use Modules\Api\app\Resources\Api\Course\CourseDetailResource;
use Modules\Api\app\Resources\Api\Course\CourseLessonShowResource;
use Modules\Api\app\Resources\Api\Course\CourseListResource;
use Modules\Api\app\Resources\Api\Course\CoursePaginateResource;
use Modules\Api\app\Resources\ButtonResource;
use Modules\Api\Enum\RouteEnum;
use Modules\Course\app\Models\Course;
use Modules\Course\app\Models\CourseAppBanner;
use Modules\Course\app\Models\CourseCategory;
use Modules\Course\app\Models\CourseComment;
use Modules\Course\app\Models\CourseFaq;
use Modules\Course\app\Models\CourseLesson;
use Modules\Course\app\Models\CourseLessonProgress;
use Modules\Course\app\Models\CourseUser;

class CourseController extends Controller
{
    use ApiHandlerTrait;

    public function index()
    {
        $latestCourses = $this->baseCourseQuery()
            ->where('is_latest', true)
            ->orderByRaw('CASE WHEN latest_sort_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('latest_sort_order')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->get();

        $popularCourses = $this->baseCourseQuery()
            ->where('is_popular', true)
            ->orderByRaw('CASE WHEN popular_sort_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('popular_sort_order')
            ->orderBy('sort_order')
            ->orderByDesc('active_purchases_count')
            ->get();

        $banners = CourseAppBanner::query()
            ->with('course:id,title,slug')
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('priority')
            ->orderBy('sort_order')
            ->get();

        $generalFaqs = CourseFaq::query()
            ->whereNull('course_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $categories = $this->categoryTreeQuery()->get();

        return $this->ok([
            'categories' => CourseCategoryTreeResource::collection($categories),
            'top_banners' => CourseAppBannerResource::collection(
                $banners->where('position', CourseAppBanner::POSITION_TOP)->values()
            ),
            'latest_courses' => [
                'title' => 'جدیدترین دوره‌ها',
                'courses' => CourseListResource::collection($latestCourses),
                'button' => ButtonResource::make([
                    'title' => 'مشاهده همه',
                    'subtitle' => 'همه دوره‌ها',
                    'route' => RouteEnum::COURSE_LIST,
                ]),
            ],
            'popular_courses' => [
                'title' => 'محبوب‌ترین دوره‌ها',
                'courses' => CourseListResource::collection($popularCourses),
                'button' => ButtonResource::make([
                    'title' => 'مشاهده همه',
                    'subtitle' => 'همه دوره‌ها',
                    'route' => RouteEnum::COURSE_LIST,
                ]),
            ],
            'middle_banners' => CourseAppBannerResource::collection(
                $banners->where('position', CourseAppBanner::POSITION_MIDDLE)->values()
            ),
            'general_faqs' => [
                'title' => 'سوالات متداول',
                'items' => FaqResource::collection($generalFaqs),
            ],
        ]);
    }

    public function show(Course $course)
    {
        $commentsPerPage = 10;
        $user = auth()->user();

        $course = Course::query()
            ->with([
                'category:id,title,image,parent_id',
                'sections' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->with([
                        'lessons' => fn ($lessonQuery) => $lessonQuery
                            ->where('is_active', true)
                            ->orderBy('sort_order'),
                    ]),
                'faqs' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
            ])
            ->withExists([
                'purchases as is_purchased' => fn ($query) => $query
                    ->where('user_id', auth()->id())
                    ->where('is_active', true)
                    ->where(function ($subQuery) {
                        $subQuery
                            ->whereNull('end_at')
                            ->orWhere('end_at', '>=', now());
                    }),
            ])
            ->whereKey($course->id)
            ->where('is_active', true)
            ->where('is_published', true)
            ->where(function ($query) {
                $query
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->firstOrFail();

        $activePurchase = $this->resolveActivePurchase($user->id, $course->id);
        $canMarkSeen = false;

        if ($activePurchase) {
            $canMarkSeen = ! CourseLessonProgress::query()
                ->where('course_user_id', $activePurchase->id)
                ->exists();
        }

        $course->setAttribute('can_mark_seen', $canMarkSeen);

        $comments = CourseComment::query()
            ->where('course_id', $course->id)
            ->where('is_approved', true)
            ->with('user')
            ->latest()
            ->paginate($commentsPerPage);

        return $this->ok([
            'course' => CourseDetailResource::make($course),
            'comments' => CourseCommentPaginateResource::make($comments),
        ]);
    }

    public function list()
    {
        $search = trim((string) request()->get('q', ''));
        $showcase = $this->resolveShowcaseFilter();
        $categoryId = request()->get('category_id');
        $perPage = max(1, min(100, (int) request()->get('per_page', self::PAGINATE)));

        $query = $this->baseCourseQuery();

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('short_description', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if (filled($categoryId)) {
            $category = CourseCategory::query()
                ->with('children:id,parent_id')
                ->findOrFail((int) $categoryId);

            $categoryIds = collect([$category->id])
                ->merge($category->children->pluck('id'))
                ->unique()
                ->values()
                ->all();

            $query->whereIn('category_id', $categoryIds);
        }

        $this->applyShowcaseFilter($query, $showcase);

        return $this->ok([
            'title' => 'لیست دوره‌ها',
            'filters' => [
                'q' => $search,
                'showcase' => $showcase,
                'showcases' => [
                    [
                        'key' => 'latest',
                        'title' => 'جدیدترین دوره‌ها',
                    ],
                    [
                        'key' => 'popular',
                        'title' => 'محبوب‌ترین دوره‌ها',
                    ],
                ],
                'category_id' => filled($categoryId) ? (int) $categoryId : null,
                'categories' => CourseCategoryTreeResource::collection($this->categoryTreeQuery()->get()),
            ],
            'courses' => CoursePaginateResource::make($query->paginate($perPage)),
        ]);
    }

    public function storeComment(Request $request, Course $course)
    {
        $course = Course::query()
            ->whereKey($course->id)
            ->where('is_active', true)
            ->where('is_published', true)
            ->where(function ($query) {
                $query
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->firstOrFail();

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:5000'],
        ], [
            'body.required' => 'متن نظر را وارد کنید.',
            'body.min' => 'متن نظر خیلی کوتاه است.',
            'body.max' => 'متن نظر بیش از حد مجاز است.',
        ]);

        $user = $request->user();

        $comment = CourseComment::query()->create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'author_name' => filled(trim((string) ($user->full_name ?? '')))
                ? $user->full_name
                : ($user->mobile ?? null),
            'body' => $validated['body'],
            'is_approved' => false,
        ]);


        return $this->created([
            'status' => true,
            'message' => 'نظر شما با موفقیت ثبت شد و پس از تایید نمایش داده می‌شود.',
            'comment' => CourseCommentResource::make($comment->load('user')),
        ]);
    }

    public function showLesson(CourseLesson $lesson)
    {
        $lesson = CourseLesson::query()
            ->with([
                'section.course:id,title,slug,is_active,is_published,published_at',
            ])
            ->whereKey($lesson->id)
            ->where('is_active', true)
            ->firstOrFail();

        $user = auth()->user();
        $activePurchase = $this->resolveActivePurchase($user->id, $lesson->section->course->id);

        if (! $lesson->is_preview && ! $activePurchase) {
            return $this->requestException([
                'status' => false,
                'message' => 'برای مشاهده این بخش باید دوره را خریداری کرده باشید.',
            ]);
        }

        $progress = null;

        if ($activePurchase) {
            $progress = CourseLessonProgress::query()
                ->where('course_user_id', $activePurchase->id)
                ->where('course_lesson_id', $lesson->id)
                ->first();
        }

        $lesson->setRelation('progress_for_user', $progress);
        $lesson->setAttribute('can_mark_seen', $activePurchase !== null && $progress === null);

        return $this->ok(CourseLessonShowResource::make($lesson));
    }

    public function markLessonSeen(CourseLesson $lesson)
    {
        $lesson = CourseLesson::query()
            ->with('section.course:id,title')
            ->whereKey($lesson->id)
            ->where('is_active', true)
            ->firstOrFail();

        $user = auth()->user();
        $activePurchase = $this->resolveActivePurchase($user->id, $lesson->section->course->id);

        if (! $activePurchase) {
            return $this->requestException([
                'status' => false,
                'message' => 'برای ثبت مشاهده این بخش باید دوره را خریداری کرده باشید.',
            ]);
        }

        $progress = CourseLessonProgress::query()->updateOrCreate(
            [
                'course_user_id' => $activePurchase->id,
                'user_id' => $user->id,
                'course_id' => $lesson->section->course->id,
                'course_lesson_id' => $lesson->id,
            ],
            [
                'completed_at' => now(),
            ]
        );

        return $this->ok([
            'status' => true,
            'message' => 'وضعیت مشاهده این بخش ثبت شد.',
            'lesson_id' => $lesson->id,
            'seen_at' => optional($progress->completed_at)->toDateTimeString(),
        ]);
    }

    private function baseCourseQuery(): Builder
    {
        return Course::query()
            ->with([
                'category:id,title,image,parent_id',
            ])
            ->withCount([
                'sections',
                'faqs',
                'comments',
                'purchases as active_purchases_count' => fn ($query) => $query
                    ->where('is_active', true)
                    ->where(function ($subQuery) {
                        $subQuery
                            ->whereNull('end_at')
                            ->orWhere('end_at', '>=', now());
                    }),
            ])
            ->withExists([
                'purchases as is_purchased' => fn ($query) => $query
                    ->where('user_id', auth()->id())
                    ->where('is_active', true)
                    ->where(function ($subQuery) {
                        $subQuery
                            ->whereNull('end_at')
                            ->orWhere('end_at', '>=', now());
                    }),
            ])
            ->where('is_active', true)
            ->where('is_published', true)
            ->where(function ($query) {
                $query
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    private function categoryTreeQuery(): Builder
    {
        return CourseCategory::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->withCount([
                'courses as courses_count' => fn ($query) => $query
                    ->where('is_active', true)
                    ->where('is_published', true)
                    ->where(function ($subQuery) {
                        $subQuery
                            ->whereNull('published_at')
                            ->orWhere('published_at', '<=', now());
                    }),
            ])
            ->with([
                'children' => fn ($query) => $query
                    ->where('is_active', true)
                    ->withCount([
                        'courses as courses_count' => fn ($courseQuery) => $courseQuery
                            ->where('is_active', true)
                            ->where('is_published', true)
                            ->where(function ($subQuery) {
                                $subQuery
                                    ->whereNull('published_at')
                                    ->orWhere('published_at', '<=', now());
                            }),
                    ])
                    ->orderBy('sort_order'),
            ])
            ->orderBy('sort_order');
    }

    private function applyShowcaseFilter(Builder $query, ?string $showcase): void
    {
        if ($showcase === 'latest') {
            $query
                ->where('is_latest', true)
                ->orderByRaw('CASE WHEN latest_sort_order IS NULL THEN 1 ELSE 0 END')
                ->orderBy('latest_sort_order')
                ->orderBy('sort_order')
                ->orderByDesc('published_at');

            return;
        }

        if ($showcase === 'popular') {
            $query
                ->where('is_popular', true)
                ->orderByRaw('CASE WHEN popular_sort_order IS NULL THEN 1 ELSE 0 END')
                ->orderBy('popular_sort_order')
                ->orderBy('sort_order')
                ->orderByDesc('active_purchases_count')
                ->orderByDesc('published_at');

            return;
        }

        $query
            ->orderBy('sort_order')
            ->orderByDesc('published_at');
    }

    private function resolveShowcaseFilter(): ?string
    {
        $showcase = trim((string) request()->get('showcase', ''));

        if ($showcase === '') {
            if (request()->boolean('is_latest')) {
                return 'latest';
            }

            if (request()->boolean('is_popular')) {
                return 'popular';
            }

            return null;
        }

        return in_array($showcase, ['latest', 'popular'], true) ? $showcase : null;
    }

    private function resolveActivePurchase(int $userId, int $courseId): ?CourseUser
    {
        return CourseUser::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('is_active', true)
            ->where(function ($query) {
                $query
                    ->whereNull('end_at')
                    ->orWhere('end_at', '>=', now()->toDateString());
            })
            ->latest('id')
            ->first();
    }
}
