<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">لیست دوره ها</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            @can('create', \Modules\Course\app\Models\Course::class)
                <a href="{{ route('admin.course.create') }}" class="btn btn-azure">افزودن دوره جدید</a>
            @endcan
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">همه دوره ها</h3>

                    <div class="card-options">
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#advanceSearch" aria-expanded="false" aria-controls="advanceSearch">
                            جست و جوی پیشرفته
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-5 collapse {{ $searchPanel }}" id="advanceSearch">
                        <form class="form-horizontal example">
                            <div class="row mb-4">
                                <label for="id" class="col-md-2 form-label">ایدی</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="id" wire:model.defer="search.id"
                                           placeholder="ایدی دوره"
                                           type="text">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="search_title" class="col-md-2 form-label">عنوان دوره</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="search_title" wire:model.defer="search.title"
                                           placeholder="عنوان دوره"
                                           type="text">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="search_slug" class="col-md-2 form-label">اسلاگ</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="search_slug" wire:model.defer="search.slug"
                                           placeholder="course-slug"
                                           type="text" dir="ltr">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="search_category" class="col-md-2 form-label">گروه‌بندی</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="search_category" wire:model.defer="search.category"
                                           placeholder="عنوان گروه‌بندی"
                                           type="text">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="search_level" class="col-md-2 form-label">سطح دوره</label>
                                <div class="col-md-10">
                                    <select class="form-control" id="search_level" wire:model.defer="search.level">
                                        <option value="">همه</option>
                                        <option value="1">آسان</option>
                                        <option value="2">متوسط</option>
                                        <option value="3">حرفه‌ای</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="search_is_latest" class="col-md-2 form-label">جدیدترین‌ها</label>
                                <div class="col-md-10">
                                    <select class="form-control" id="search_is_latest" wire:model.defer="search.is_latest">
                                        <option value="">همه</option>
                                        <option value="1">فقط جدیدترین‌ها</option>
                                        <option value="0">غیرجدیدترین</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="search_is_popular" class="col-md-2 form-label">محبوب‌ترین‌ها</label>
                                <div class="col-md-10">
                                    <select class="form-control" id="search_is_popular" wire:model.defer="search.is_popular">
                                        <option value="">همه</option>
                                        <option value="1">فقط محبوب‌ترین‌ها</option>
                                        <option value="0">غیرمحبوب‌ترین</option>
                                    </select>
                                </div>
                            </div>

                            <button class="btn btn-primary" type="button" wire:click="startSearch"
                                    wire:loading.class="bg-gray btn-loading disabled">جست و جو
                            </button>
                        </form>
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table text-nowrap text-md-nowrap table-bordered" wire:loading.class="op-0-3">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">تصویر</th>
                                <th scope="col">عنوان</th>
                                <th scope="col">گروه‌بندی</th>
                                <th scope="col">سطح</th>
                                <th scope="col">خریدار فعال</th>
                                <th scope="col">قیمت</th>
                                <th scope="col">مدت</th>
                                <th scope="col">زیر‌بخش‌ها</th>
                                <th scope="col">سوالات متداول</th>
                                <th scope="col">نظرات</th>
                                <th scope="col">اولویت</th>
                                <th scope="col">ویترین</th>
                                <th scope="col">وضعیت</th>
                                <th scope="col">عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($courses->isNotEmpty())
                                @foreach($courses as $course)
                                    <tr>
                                        <td class="text-center">{{ $course->id }}</td>
                                        <td class="text-center">
                                            @if($course->thumbnail)
                                                <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}"
                                                     style="width: 56px; height: 56px; object-fit: cover; border-radius: 8px;">
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $course->title }}</td>
                                        <td>{{ $course->category?->full_title ?? '-' }}</td>
                                        <td><span class="badge bg-secondary">{{ $course->level_label }}</span></td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.course.purchased', ['search' => ['course_id' => $course->id, 'is_active' => 1]]) }}"
                                               class="badge bg-success text-decoration-none">
                                                {{ $course->active_purchases_count }} نفر
                                            </a>
                                        </td>
                                        <td>
                                            <div>{{ number_format($course->final_price ?? $course->price) }}</div>
                                            @if(!empty($course->discounted_price))
                                                <small class="text-muted text-decoration-line-through">{{ number_format($course->price) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $course->duration_minutes }} دقیقه</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.course.content', $course) }}"
                                               class="badge bg-info text-decoration-none">
                                                {{ $course->sections_count }} زیر‌بخش
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.course.faqs', $course) }}"
                                               class="badge bg-primary text-decoration-none">
                                                {{ $course->faqs_count }} سوال
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.course.comments', $course) }}"
                                               class="text-decoration-none d-inline-flex flex-column gap-1 align-items-center">
                                                <span class="badge bg-warning text-dark">جدید {{ $course->new_comments_count }}</span>
                                                <span class="badge bg-success">تایید شده {{ $course->approved_comments_count }}</span>
                                            </a>
                                        </td>
                                        <td>{{ $course->sort_order }}</td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                @if($course->is_latest)
                                                    <span class="badge bg-primary">
                                                        جدیدترین
                                                        @if($course->latest_sort_order)
                                                            #{{ $course->latest_sort_order }}
                                                        @endif
                                                    </span>
                                                @endif
                                                @if($course->is_popular)
                                                    <span class="badge bg-danger">
                                                        محبوب‌ترین
                                                        @if($course->popular_sort_order)
                                                            #{{ $course->popular_sort_order }}
                                                        @endif
                                                    </span>
                                                @endif
                                                @if(! $course->is_latest && ! $course->is_popular)
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($course->is_active)
                                                <span class="badge bg-success">فعال</span>
                                            @else
                                                <span class="badge bg-danger">غیرفعال</span>
                                            @endif

                                            @if($course->is_published)
                                                <span class="badge bg-primary">منتشر شده</span>
                                            @else
                                                <span class="badge bg-warning">پیش نویس</span>
                                            @endif

                                            @if($course->is_purchasable)
                                                <span class="badge bg-info">قابل خرید</span>
                                            @else
                                                <span class="badge bg-secondary">غیرقابل خرید</span>
                                            @endif
                                        </td>
                                        <td>
                                            @canany(['update','delete'], $course)
                                                <div class="btn-group mt-2 mb-2">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        عملیات <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        @can('update', $course)
                                                            <li>
                                                                <a href="{{ route('admin.course.edit', $course) }}">ویرایش دوره</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ route('admin.course.content', $course) }}">مدیریت بخش‌ها</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ route('admin.course.faqs', $course) }}">سوالات متداول</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ route('admin.course.comments', $course) }}">نظرات کاربران</a>
                                                            </li>
                                                        @endcan
                                                        @can('delete', $course)
                                                            <li>
                                                                <a class="delete_confirm_alert"
                                                                   data-label="حذف دوره"
                                                                   data-id="{{ $course->id }}"
                                                                   href="">حذف</a>
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            @else
                                                <div class="btn-group mt-2 mb-2">
                                                    <button type="button" class="btn btn-default dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        عملیات <span class="caret"></span>
                                                    </button>
                                                </div>
                                            @endcanany
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="15" class="text-center">
                                        <div class="alert alert-info">
                                            هیچ دوره‌ای یافت نشد
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{ $courses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{admin_asset('plugins/sweet-alert/sweetalert.min.js')}}"></script>
    <script src="{{admin_asset('plugins/sweet-alert/admin.sweetalert.js')}}"></script>

    <script>
        var myCollapsible = document.getElementById('advanceSearch')
        myCollapsible.addEventListener('show.bs.collapse', function () {
            @this.set('searchPanel', 'show');
        });
        myCollapsible.addEventListener('hide.bs.collapse', function () {
            @this.set('searchPanel', '');
        })
    </script>
@endpush
