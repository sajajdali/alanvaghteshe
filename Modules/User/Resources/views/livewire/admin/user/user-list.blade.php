<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">مدیریت کاربران</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            @can('create', \Modules\User\Entities\User::class)
                <a href="{{ route('admin.user.create') }}" class="btn btn-azure">افزودن کاربر جدید</a>
            @endcan
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    @can('user')
                        <h3 class="card-title">همه کاربران</h3>
                    @else
                        <h3 class="card-title">کاربران شما</h3>
                        @endif
                        <div class="card-options">
                            @if (!empty($search))
                                <button class="btn btn-secondary me-1" type="button" data-bs-toggle="collapse"
                                    wire:click='resetSearch' data-bs-target="#advanceSearch" aria-expanded="false"
                                    aria-controls="advanceSearch">
                                    نمایش همه
                                </button>
                            @endif
                            <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#advanceSearch" aria-expanded="false" aria-controls="advanceSearch">
                                جست و جوی پیشرفته
                            </button>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-5 collapse {{ $searchPanel }}" id="advanceSearch">
                            <form class="form-horizontal example">
                                <div class="row mb-4">
                                    <label for="ID" class="col-md-2 form-label">ایدی</label>
                                    <div class="col-md-10">
                                        <input class="form-control" id="ID" wire:model="search.id"
                                            placeholder="ایدی کاربر مورد نظر" type="text">
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="email" class="col-md-2 form-label">ایمیل</label>
                                    <div class="col-md-10">
                                        <input class="form-control" id="email" wire:model="search.email"
                                            placeholder="ایمیل کاربر مورد نظر" type="text">
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="email" class="col-md-2 form-label">موبایل</label>
                                    <div class="col-md-10">
                                        <input class="form-control" id="email" wire:model="search.mobile"
                                            placeholder="موبایل کاربر مورد نظر" type="text">
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="first_name" class="col-md-2 form-label">نام</label>
                                    <div class="col-md-10">
                                        <input class="form-control" id="first_name" wire:model="search.first_name"
                                            placeholder="نام کاربر مورد نظر" type="text">
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="last_name" class="col-md-2 form-label">نام خانوادگی</label>
                                    <div class="col-md-10">
                                        <input class="form-control" id="last_name" wire:model="search.last_name"
                                            placeholder="نام خانوادگی کاربر مورد نظر" type="text">
                                    </div>
                                </div>
                                <div class="row mb-4 d-flex justify-content-end">
                                    <button class="btn btn-primary" type="button" wire:click="startSearch"
                                        wire:loading.class="bg-gray btn-loading disabled" wire:click="updateOrCreate">جست و
                                        جو
                                    </button>
                                </div>

                            </form>
                        </div>
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap text-md-nowrap table-bordered" wire:loading.class="op-0-3">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">نام کاربر</th>
                                        <th scope="col">موبایل</th>
                                        <th scope="col">ایمیل</th>
                                        <th scope="col">نقش کاربر</th>
                                        <th scope="col">وضعیت</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($users->isNotEmpty())
                                        @foreach ($users as $user)
                                            <tr wire:key="user_{{ $user->id }}">
                                                <td>
                                                    @can('user.documentte', $user)
                                                        <a href="{{ route('admin.user.document', $user) }}">
                                                        @endcan
                                                        {{ $user->id }}
                                                        @can('user.documentte', $user)
                                                        </a>
                                                    @endcan
                                                </td>
                                                <td>
                                                    <div class="media mt-4 profile-footer align-items-center text-start">
                                                        <div class="media-user me-2">
                                                            <div class="main-img-user">
                                                                <img alt="{{ $user->full_name }}"
                                                                    title="{{ $user->full_name }}"
                                                                    class="rounded-circle avatar-md"
                                                                    src="{{ $user->avatar ?? asset('assets/admin/images/svgs/user.svg') }}">
                                                            </div>
                                                        </div>

                                                        <div class="media-body">
                                                            @can('user.documentte', $user)
                                                                <a href="{{ route('admin.user.document', $user) }}">
                                                                    <h6 class="mb-0">
                                                                        {{ $user->full_name }}
                                                                    </h6>
                                                                </a>
                                                            @else
                                                                <h6 class="mb-0 text-dark-light">
                                                                    {{ $user->full_name }}
                                                                </h6>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                    @if ($user->telegram_chat_id)
                                                        <span class="badge bg-danger-gradient my-1 txt-white">telegram code
                                                            : {{ $user->telegram_chat_id }}</span>
                                                    @endif

                                                </td>
                                                <td> {{ $user->mobile }}</td>
                                                <td> {{ $user->email }}</td>
                                                <td>
                                                    @foreach ($user->roles as $index => $role)
                                                        <span
                                                            class="badge bg-{{ $index === 0 ? 'info' : ($index === 1 ? 'success' : 'danger') }} my-1 text-bold">{{ $role->name }}</span>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @canany(['update', 'delete'], $user)
                                                        <div class="btn-group mt-2 mb-2">
                                                            <button type="button" class="btn btn-primary dropdown-toggle"
                                                                data-bs-toggle="dropdown">
                                                                عملیات <span class="caret"></span>
                                                            </button>
                                                            <ul class="dropdown-menu" role="menu">
                                                                @can('update', $user)
                                                                    <li>
                                                                        <a href="{{ route('admin.user.edit', $user) }}">
                                                                            <i class="fa fa-pencil-square-o text-info me-1"
                                                                                aria-hidden="true"></i>
                                                                            <span>ویرایش</span>
                                                                        </a>
                                                                    </li>

                                                                @endcan
                                                                @can('user.documentte', $user)
                                                                    <li>
                                                                        <a href="{{ route('admin.user.document', $user) }}">
                                                                            <i class="fa fa-address-card-o text-primary me-1"
                                                                                aria-hidden="true"></i>
                                                                            مشاهده
                                                                            پرونده
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a target="_blank"
                                                                            href="{{ route('referral.user', $user->invitation_code) }}">
                                                                            <i class="fa fa-file-pdf-o text-secondary me-1"
                                                                                aria-hidden="true"></i>
                                                                            صفحه معرفی
                                                                        </a>
                                                                    </li>
                                                                @endcan
                                                                @if ($user->hasRole(['پشتیبانی اپ', 'کاربران']))
                                                                    <li>
                                                                        <a
                                                                            href="{{ route('admin.user.telegram.create', $user->id) }}">
                                                                            <i class="fa fa-android text-info me-1"
                                                                                aria-hidden="true"></i>
                                                                            ایجاد دسترسی به ربات تلگرام
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                                @can('user.sendAppLink')
                                                                    <li>
                                                                        <a wire:click="sendWebAppLink('{{ $user->id }}')" style="cursor: pointer;">
                                                                            <i class="fa fa-envelope-o text-secondary me-1"
                                                                                aria-hidden="true"></i>
                                                                            ارسال لینک اپ
                                                                        </a>
                                                                    </li>
                                                @endif
                                                @can('delete', $user)
                                                    @unless ($user->id === 1)
                                                        <li><a class="delete_confirm_alert" data-label="کاربر"
                                                                data-id="{{ $user->id }}" href="">
                                                                <i class="fa fa-trash text-danger me-1" aria-hidden="true"></i>
                                                                حذف</a>
                                                        </li>
                                                    @else
                                                        <li><a class="admin_sweet_alert op-0-4" data-title="خطا" data-type="error"
                                                                data-description="امکان حذف این کاربر وجود ندارد." href="#">حذف</a>
                                                        </li>
                                                    @endunless
                                                @endcan
                                                </ul>
                                </div>
                            @else
                                <div class="btn-group mt-2 mb-2">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-bs-toggle="dropdown">
                                        عملیات <span class="caret"></span>
                                    </button>
                                </div>
                            @endcanany
                            </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="alert alert-info">
                                        هیچ کاربری یافت نشد
                                    </div>
                                </td>
                            </tr>
                            @endif
                            </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center">
                            {{ $users->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

        @push('scripts')
            <script>
                var myCollapsible = document.getElementById('advanceSearch')
                myCollapsible.addEventListener('show.bs.collapse', function() {
                    @this.set('searchPanel', 'show');
                });
                myCollapsible.addEventListener('hide.bs.collapse', function() {
                    @this.set('searchPanel', '');
                })
            </script>
        @endpush
