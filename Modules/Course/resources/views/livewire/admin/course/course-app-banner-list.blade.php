<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">بنرهای اپلیکیشن</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <a href="{{ route('admin.course.banners.create') }}" class="btn btn-azure">افزودن بنر جدید</a>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">لیست بنرهای اپ</h3>
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
                                <label class="col-md-2 form-label" for="search_title">نام بنر</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="search_title" wire:model.defer="search.title" type="text" placeholder="نام بنر">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-md-2 form-label" for="search_course">دوره</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="search_course" wire:model.defer="search.course" type="text" placeholder="عنوان دوره">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-md-2 form-label" for="search_position">جایگاه</label>
                                <div class="col-md-10">
                                    <select class="form-control" id="search_position" wire:model.defer="search.position">
                                        <option value="">همه</option>
                                        @foreach($positions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-md-2 form-label" for="search_is_active">وضعیت</label>
                                <div class="col-md-10">
                                    <select class="form-control" id="search_is_active" wire:model.defer="search.is_active">
                                        <option value="">همه</option>
                                        <option value="1">فعال</option>
                                        <option value="0">غیرفعال</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-primary" type="button" wire:click="startSearch">جست و جو</button>
                        </form>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table text-nowrap text-md-nowrap table-bordered text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>عکس</th>
                                <th>نام بنر</th>
                                <th>دوره مقصد</th>
                                <th>جایگاه</th>
                                <th>ترتیب</th>
                                <th>اولویت</th>
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($banners as $banner)
                                <tr>
                                    <td>{{ $banner->id }}</td>
                                    <td>
                                        <img src="{{ $banner->image }}" alt="{{ $banner->title }}" style="width: 88px; height: 56px; object-fit: cover; border-radius: 8px;">
                                    </td>
                                    <td>{{ $banner->title }}</td>
                                    <td>{{ $banner->course?->title ?? '-' }}</td>
                                    <td>{{ $banner->position_label }}</td>
                                    <td>{{ $banner->sort_order }}</td>
                                    <td>{{ $banner->priority }}</td>
                                    <td>
                                        <span class="badge {{ $banner->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $banner->is_active ? 'فعال' : 'غیرفعال' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group mt-2 mb-2">
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                عملیات <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="{{ route('admin.course.banners.edit', $banner) }}">ویرایش</a></li>
                                                <li>
                                                    <a href="#" wire:click.prevent="delete({{ $banner->id }})" wire:confirm="آیا از حذف این بنر مطمئن هستید؟">
                                                        حذف
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">
                                        <div class="alert alert-info mb-0">هنوز بنری ثبت نشده است.</div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $banners->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
