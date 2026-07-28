<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">لیست دستگاه های اضافه شد</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            @can('create', \Modules\Admin\app\Models\WhatsappSession::class)
                <a href="{{ route('admin.whatsapp.create') }}" class="btn btn-azure">افزودن دستگاه جدید</a>
            @endcan
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">همه دستگاه ها</h3>

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
                                <label for="ID" class="col-md-2 form-label">ایدی</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="id" wire:model.defer="search.id"
                                           placeholder="ایدی درخواست مورد نظر"
                                           type="text">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="ID" class="col-md-2 form-label">نام دستگاه</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="name" wire:model.defer="search.name"
                                           placeholder="نام دستگاه مورد نظر"
                                           type="text">
                                </div>
                            </div>

                            <button class="btn btn-primary" type="button" wire:click="startSearch"
                                    wire:loading.class="bg-gray btn-loading disabled">جست و
                                جو
                            </button>
                        </form>
                    </div>
                    <div class="table-responsive mb-3" wire:init="loadStatuses">
                        <table class="table text-nowrap text-md-nowrap table-bordered" wire:loading.class="op-0-3">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">نام دستگاه</th>
                                <th scope="col">عملیات</th>
                                <th scope="col">وضعیت</th>

                                <th scope="col">حذف</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($WhatsappSessions->isNotEmpty())
                                @foreach($WhatsappSessions as $WhatsappSession)
                                    <tr>
                                        <td class="text-center">{{ $WhatsappSession->id }}</td>


                                        <td>
                                            {{ $WhatsappSession->name }}
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-info"
                                                    wire:click.prevent="start({{ $WhatsappSession->id }})">
                                                <i class="fe fe-play me-2"></i> استارت
                                            </button>

                                            <button type="button"
                                                    class="btn btn-warning"
                                                    wire:click.prevent="stop({{ $WhatsappSession->id }})">
                                                <i class="fe fe-pause me-2"></i> توقف
                                            </button>
                                            <button type="button"
                                                    class="btn btn-danger"
                                                    wire:click.prevent="logout({{ $WhatsappSession->id }})">
                                                <i class="fe fe-log-out me-2"></i> خروج
                                            </button>
                                            <button type="button"
                                                    class="btn btn-secondary"
                                                    wire:click.prevent="restart({{ $WhatsappSession->id }})">
                                                <i class="fe fe-refresh-ccw me-2"></i> ری‌استارت
                                            </button>
                                            <button type="button"
                                                    class="btn btn-outline-info"
                                                    title="نمایش وضعیت"
                                                    wire:click.prevent="showStatus({{ $WhatsappSession->id }})">
                                                <i class="fe fe-activity me-2"></i> وضعیت
                                            </button>

                                            <button type="button"
                                                    class="btn btn-outline-secondary"
                                                    wire:click.prevent="scanQr({{ $WhatsappSession->id }})">
                                                <i class="fe fe-smartphone me-2"></i> اسکن QR
                                            </button>
                                            <button type="button"
                                                    class="btn btn-outline-success"
                                                    wire:click.prevent="showProfile({{ $WhatsappSession->id }})">
                                                <i class="fe fe-user me-2"></i> پروفایل
                                            </button>
                                            <button type="button"
                                                    class="btn btn-outline-dark"
                                                    wire:click.prevent="screenshot({{ $WhatsappSession->id }})">
                                                <i class="fe fe-image me-2"></i> اسکرین‌شات
                                            </button>
                                            <button type="button"
                                                    class="btn btn-outline-primary"
                                                    wire:click.prevent="openSendModal({{ $WhatsappSession->id }})">
                                                <i class="fe fe-send me-2"></i> ارسال پیام
                                            </button>
                                        </td>
                                        @php
                                            $s = $statuses[$WhatsappSession->id] ?? null;
                                            $map = [
                                                'STARTED'      => 'bg-success',
                                                'STARTING'     => 'bg-primary',
                                                'STOPPED'      => 'bg-secondary',
                                                'SCAN_QR_CODE' => 'bg-warning text-dark',
                                                'خطا'          => 'bg-danger',
                                                null           => 'bg-light text-dark'
                                            ];
                                            $cls = $map[$s] ?? 'bg-light text-dark';
                                            $label = $s ?? ($statusesLoaded ? '—' : 'در حال لود…');
                                        @endphp
                                        <td>
                                            <span class="badge {{ $cls }}">{{ $label }}</span>
                                        </td>
                                        <td>
                                            @canany(['update','delete'],$WhatsappSession)
                                                <div class="btn-group mt-2 mb-2">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        عملیات <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        @can('update',$WhatsappSession)
                                                            <li>
                                                                <a href="{{ route('admin.whatsapp.edit',$WhatsappSession) }}">ویرایش
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('delete',$WhatsappSession)
                                                            <li><a class="delete_confirm_alert"
                                                                   data-label="حذف "
                                                                   data-id="{{ $WhatsappSession->id }}"
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
                                    <td colspan="6" class="text-center">
                                        <div class="alert alert-info">
                                            هیچ دستگاهی یافت نشد
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{ $WhatsappSessions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- QR Modal --}}
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel">QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>
                <div class="modal-body text-center">
                    @if($qrModalSrc)
                        <img src="{{ $qrModalSrc }}" alt="QR Code" class="img-fluid" />
                    @else
                        <div class="text-muted">در حال آماده‌سازی...</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        بستن
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel">اطلاعات پروفایل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>
                <div class="modal-body text-center">
                    @if($profileData)
                        <img src="{{ $profileData['picture'] ?? '' }}" class="img-thumbnail mb-3" alt="Profile Picture" style="max-width: 150px;">
                        <h5>{{ $profileData['name'] ?? '' }}</h5>
                        <p class="text-muted">{{ $profileData['id'] ?? '' }}</p>
                    @else
                        <div class="text-muted">در حال بارگذاری...</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="screenshotModal" tabindex="-1" aria-labelledby="screenshotModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="screenshotModalLabel">اسکرین‌شات سشن</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>
                <div class="modal-body text-center">
                    @if($screenshotModalSrc)
                        <img src="{{ $screenshotModalSrc }}" alt="Screenshot" class="img-fluid w-100"
                             style="max-height:80vh; object-fit:contain;">
                    @else
                        <div class="text-muted">در حال بارگذاری...</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="sendModal" tabindex="-1" aria-labelledby="sendModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendModalLabel">ارسال پیام واتس‌اپ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>

                <div class="modal-body">
                    @if(!$sendAllowed && $sendBlockMsg)
                        <div class="alert alert-warning mb-3">
                            {{ $sendBlockMsg }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">گیرنده (chatId)</label>
                        <input type="text" class="form-control" placeholder="مثال: 98912XXXXXXX@c.us"
                               wire:model.defer="sendChatId" @disabled(!$sendAllowed)>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">متن پیام</label>
                        <textarea class="form-control" rows="4"
                                  wire:model.defer="sendBody" @disabled(!$sendAllowed)></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                    <button type="button" class="btn btn-primary ms-2"
                            wire:click="submitSend" @disabled(!$sendAllowed)>
                        ارسال
                    </button>
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
            @this.set('searchPanel', 'show')
            ;
        });
        myCollapsible.addEventListener('hide.bs.collapse', function () {
            @this.set('searchPanel', '')
            ;
        })
    </script>
    <script>
        window.addEventListener('swal', function (e) {
            const d = e.detail || {};
            swal({ title: d.title || '', text: d.text || '', icon: d.icon || 'info', button: 'باشه' });
        });
        // کد مدیریت collapse شما دست‌نخورده
    </script>
    <script>
        window.addEventListener('show-qr-modal', function () {
            const el = document.getElementById('qrModal');
            if (!el) return;
            const modal = new bootstrap.Modal(el);
            modal.show();
        });
    </script>

    <script>
        window.addEventListener('show-profile-modal', function () {
            const el = document.getElementById('profileModal');
            if (!el) return;
            const modal = new bootstrap.Modal(el);
            modal.show();
        });
    </script>
    <script>
        window.addEventListener('show-screenshot-modal', function () {
            const el = document.getElementById('screenshotModal');
            if (!el) return;
            const modal = new bootstrap.Modal(el);
            modal.show();
        });
    </script>
    <script>
        window.addEventListener('show-send-modal', function () {
            const el = document.getElementById('sendModal');
            if (!el) return;
            const modal = new bootstrap.Modal(el);
            modal.show();
        });
    </script>
@endpush
