<div>
    @include('admin::layouts.components.alert')
    @include('admin::components.loading')
    @include('admin::components.userLogModal')
    <div class="row row-sm mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body text-center">
                    <div class="my-5" wire:ignore.self>
                        <div class="row mb-4">
                            <label for="ID" class="col-md-2 form-label"><strong>فیلتر</strong></label>
                            <div class="col-md-8">
                                <select class="form-select" wire:model.live="search" aria-label="Default select example">
                                    <option value="">انتخاب کنید...</option>
                                    @foreach (\Modules\Admin\app\Enums\UserConditionFiltersEnum::cases() as $value)
                                        <option value="{{ $value->value }}">{{ $value->getName() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-info" type="button" wire:click="startSearch"
                                    wire:loading.class="disabled">اعمال فیلتر
                                </button>
                            </div>
                        </div>
                    </div>
                    @if (!is_null($users) && $users->isNotEmpty())
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap text-md-nowrap table-bordered" wire:loading.class="op-0-3">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">نام</th>
                                        <th scope="col">موبایل</th>
                                        <th scope="col">سابقه پشتیبانی</th>
                                        <th scope="col">ثبت تماس</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($users as $user)
                                        <tr wire:key="supporter_{{ $user->id }}">
                                            <td>{{ $user->id }}</td>
                                            <td class="d-flex justify-content-between">
                                                <span>{{ $user->fullName }}</span>
                                                @if (!empty($user->lastSupporterCalled) && $user->lastSupporterCalled != "null")
                                                    <span class="bubble-chip">
                                                        {{ $user->lastSupporterCalledName() }}
                                                        <!-- remove button -->
                                                        @if (auth()->user()->isAdmin())
                                                            <button type="button" class="bubble-close swal_confirm"
                                                                data-action="removeLog"
                                                                data-description="اخرین تماس پشتیابنی مربوط به این کاربر حذف شود؟"
                                                                data-id="{{ $user->id }}">
                                                                &times;
                                                            </button>
                                                        @endif
                                                    </span>
                                                @else
                                                    <span></span>
                                                @endif
                                            </td>
                                            <td>{{ $user->mobile ?? '-' }}</td>
                                            <td>
                                                @if ($user->logFor->isNotEmpty())
                                                    <button class="btn btn-sm btn-primary-light" data-bs-toggle="modal"
                                                        wire:click="assignUserForModal('{{ $user->id }}')"
                                                        data-bs-target="#userLogModal">
                                                        مشاهده
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-light " disabled>
                                                        بدون پشتیبانی
                                                    </button>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-info-light swal_confirm_call" data-action="callUser"
                                                    data-description="برای {{ $user->fullName }} یک تماس ثبت شود؟"
                                                    data-id="{{ $user->id }}">
                                                    <i class="fa fa-phone" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center">
                            {{ $users->onEachSide(1)->links() }}
                        </div>
                    @elseif(!is_null($search))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            کاربری با این مشخصات یافت نشد!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(function (e) {
            $('body').on('click', '.swal_confirm', function (e) {
                e.preventDefault();
                let $label = $(this).data('label');
                let $id = $(this).data('id');
                let $action = $(this).data('action');
                swal({
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-danger'
                        },
                        buttonsStyling: false,
                        title: "از حذف این مورد اطمینان دارید؟",
                        text: "آیا میخواهید حذف کنید؟",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn btn-danger",
                        confirmButtonText: "بله حذف شود",
                        cancelButtonText: "خیر",
                        closeOnConfirm: true
                    },
                    function () {
                        Livewire.dispatch($action, {model: $id});
                        //show loading animation
                    });
            });
            $('body').on('click', '.swal_confirm_call', function (e) {
                e.preventDefault();
                let $label = $(this).data('label');
                let $id = $(this).data('id');
                let $action = $(this).data('action');
                swal({
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-danger'
                        },
                        buttonsStyling: false,
                        title: "تماس برقرار شود",
                        text: "میخواهید ثبت شود؟",
                        type: "info",
                        showCancelButton: true,
                        confirmButtonClass: "btn btn-danger",
                        confirmButtonText: "بله  ",
                        cancelButtonText: "خیر",
                        closeOnConfirm: true
                    },
                    function () {
                        Livewire.dispatch($action, {model: $id});
                        //show loading animation
                    });
            });
        });
    </script>
@endpush
@push('styles')
    <style>
        .break-anywhere {
            white-space: normal;
            /* allow wrapping */
            overflow-wrap: anywhere;
            /* break very long tokens/URLs */
            word-break: break-word;
            /* legacy fallback */
        }

        /* blue rounded "button" look on a span */
        .bubble-chip {
            position: relative;
            display: inline-block;
            padding: .30rem 1.1rem;
            border-radius: 12px;
            color: #fff;
            font-weight: 600;
            background: linear-gradient(180deg, #a2c9ff, #76a8f3);
        }

        /* the red circular X as a *real button* */
        .bubble-close {
            position: absolute;
            inset-block-start: -10px;
            /* RTL/LTR safe 'top'  */
            inset-inline-start: -10px;
            /* RTL/LTR safe 'left' */
            inline-size: 26px;
            block-size: 26px;
            border-radius: 999px;
            border: 2px solid #fff;
            /* white ring */
            background: linear-gradient(135deg, #ff2d55, #ff3b30);
            color: #fff;
            font-size: 16px;
            line-height: 1;
            display: grid;
            place-items: center;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(255, 45, 85, .35);
            transition: transform .15s ease, filter .2s ease, box-shadow .2s ease;
        }

        .bubble-close:hover {
            filter: brightness(1.05);
            transform: scale(1.05);
        }

        .bubble-close:active {
            transform: scale(.95);
        }

        .bubble-close:focus-visible {
            outline: 2px solid #fff;
            outline-offset: 2px;
        }

        /* optional: place X on the top-right instead */
        .bubble-chip.badge-right .bubble-close {
            inset-inline-start: auto;
            inset-inline-end: -10px;
        }
    </style>
@endpush
