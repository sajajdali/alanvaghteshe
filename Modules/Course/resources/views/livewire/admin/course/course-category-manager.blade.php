<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">گروه‌بندی دوره‌ها</h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between">
                    <h3 class="card-title">{{ $editingCategory ? 'ویرایش گروه‌بندی' : 'افزودن گروه‌بندی' }}</h3>
                    @if($editingCategory)
                        <button class="btn btn-sm btn-outline-secondary" type="button" wire:click="resetForm">انصراف</button>
                    @endif
                </div>
                <div class="card-body">
                    <form wire:submit.prevent>
                        <div class="mb-3">
                            <label for="title">عنوان</label>
                            <input type="text" id="title" class="form-control @error('title') is-invalid @enderror" wire:model.live="title">
                            @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="slug">اسلاگ</label>
                            <input type="text" id="slug" class="form-control @error('slug') is-invalid @enderror" wire:model.live="slug" dir="ltr">
                            @error('slug')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <x-admin.core.form.image-upload
                                label="تصویر گروه‌بندی"
                                uploadedPhotoUrl="{{ $uploadedPhotoUrl }}"
                                uploadedFileName="{{ $uploadedFileName }}"
                                uploadedFileType="{{ $uploadedFileType }}"
                                deleteAction="deletePhoto"
                                model="photo"
                                id="categoryPhoto" />
                            @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('photo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="parent_id">دسته والد</label>
                            <select id="parent_id" class="form-control @error('parent_id') is-invalid @enderror" wire:model.live="parent_id">
                                <option value="">بدون والد</option>
                                @foreach($parentOptions as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                                @endforeach
                            </select>
                            @error('parent_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">فقط یک سطح زیرشاخه مجاز است.</small>
                        </div>
                        <div class="mb-3">
                            <label for="sort_order">ترتیب</label>
                            <input type="number" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" wire:model.live="sort_order" dir="ltr">
                            @error('sort_order')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="custom-control custom-switch pr-4">
                                <input type="checkbox" class="custom-control-input" id="is_active" wire:model.live="is_active">
                                <label class="custom-control-label me-2 ms-6" for="is_active">فعال باشد</label>
                            </div>
                        </div>
                        <button class="btn btn-primary" type="button" wire:click="save">
                            {{ $editingCategory ? 'ویرایش گروه‌بندی' : 'ثبت گروه‌بندی' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">لیست گروه‌بندی‌ها</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-nowrap text-md-nowrap table-bordered text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>تصویر</th>
                                <th>عنوان</th>
                                <th>والد</th>
                                <th>سطح</th>
                                <th>ترتیب</th>
                                <th>تعداد دوره</th>
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>
                                        @if($category->image)
                                            <img src="{{ $category->image }}" alt="{{ $category->title }}" style="width: 56px; height: 56px; object-fit: cover; border-radius: 10px;">
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $category->title }}</td>
                                    <td>{{ $category->parent?->title ?? '-' }}</td>
                                    <td>{{ $category->depth }}</td>
                                    <td>{{ $category->sort_order }}</td>
                                    <td>{{ $category->courses_count }}</td>
                                    <td>
                                        <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $category->is_active ? 'فعال' : 'غیرفعال' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group mt-2 mb-2">
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                عملیات <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="#" wire:click.prevent="edit({{ $category->id }})">ویرایش</a></li>
                                                <li><a href="#" wire:click.prevent="delete({{ $category->id }})" wire:confirm="آیا از حذف این گروه‌بندی مطمئن هستید؟">حذف</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">
                                        <div class="alert alert-info mb-0">هنوز گروه‌بندی‌ای ثبت نشده است.</div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
