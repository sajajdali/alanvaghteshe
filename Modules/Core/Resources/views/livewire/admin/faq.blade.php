<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">
                سوالات متداول اپلیکیشن
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">سوالات متداول اپلیکیشن</h3>
                </div>
                <div class="card-body color-acc">
                    <div class="alert alert-info">
                        سوالات این بخش در FAQ عمومی اپلیکیشن نمایش داده می‌شوند و به دوره خاصی وابسته نیستند.
                    </div>
                    @forelse($faqs as $faq)
                        <div aria-multiselectable="true"
                             class="@if($faq->status) accordion-success @else accordion-danger @endif mb-2"
                             id="accordion{{ $faq->id }}" role="tablist">
                            <div class="card mb-0">
                                <div class="card-header border-bottom-0" id="heading5" role="tab">
                                    <a
                                        class="accor-style2 collapsed" aria-controls="collapse{{ $faq->id }}"
                                        aria-expanded="false"
                                        data-bs-toggle="collapse" href="#collapse{{ $faq->id }}"><i
                                            class="fe fe-plus-circle me-2"></i>
                                        {{ $faq->question }} - <span
                                            class="badge">{{ $faq->status ? 'فعال' : 'غیرفعال' }}</span>
                                    </a>
                                    @can('update',$faq)
                                        @if($loop->count > 1)
                                            <div class="card-options">
                                                @if(!$loop->first)
                                                    <a href="#"
                                                       wire:click="changeOrder({{ $faq->id }},{{ $faqs[$loop->index - 1]->id }})"
                                                       wire:target="changeOrder({{ $faq->id }},{{ $faqs[$loop->index - 1]->id }})"
                                                       wire:loading.attr="disabled"
                                                       wire:loading.class="btn-loading btn-dark"
                                                       class="card-options-up" data-bs-toggle="card-collapse"><i
                                                            class="fe fe-chevron-up text-white"></i></a>
                                                @endif
                                                @if(!$loop->last)
                                                    <a href="#"
                                                       wire:click="changeOrder({{ $faq->id }},{{ $faqs[$loop->index + 1]->id }})"
                                                       class="card-options-down" data-bs-toggle="card-collapse"><i
                                                            class="fe fe-chevron-down text-white"></i></a>
                                                @endif
                                            </div>
                                        @endif
                                    @endcan
                                </div>
                                <div aria-labelledby="heading5" class="collapse"
                                     data-bs-parent="#accordion{{ $faq->id }}"
                                     id="collapse{{ $faq->id }}"
                                     role="tabpanel">
                                    <div class="card-body">
                                        {!!  nl2br($faq->answer) !!}
                                    </div>
                                    <div class="card-footer">
                                        <div class="btn-list">
                                            @can('update',$faq)
                                                <a href="javascript:void(0)"
                                                   wire:loading.attr="disabled"
                                                   wire:loading.class="btn-loading btn-dark"
                                                   wire:loading.class.remove="btn-primary"
                                                   class="btn btn-primary"
                                                   wire:click="edit({{ $faq->id }})">ویرایش</a>
                                                <a href="javascript:void(0)"
                                                   wire:loading.attr="disabled"
                                                   wire:loading.class="btn-loading btn-dark"
                                                   class="btn @if($faq->status) btn-danger @else btn-success @endif"
                                                   wire:click="toggleStatus({{ $faq->id }})">
                                                    @if($faq->status)
                                                        غیرفعال کردن
                                                    @else
                                                        فعال کردن
                                                    @endif
                                                </a>
                                            @endcan
                                            @can('delete',$faq)
                                                <a href="javascript:void(0)"
                                                   wire:loading.attr="disabled"
                                                   wire:loading.class="btn-loading btn-dark"
                                                   wire:loading.class.remove="btn-secondary"
                                                   class="btn btn-secondary delete_confirm_alert"
                                                   data-label="ورزش"
                                                   data-id="{{ $faq->id }}"
                                                >حذف</a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- accordion -->
                    @empty
                        <div class="alert alert-warning alert-dismissible fade show" role="alert"><span
                                class="alert-inner--icon me-2"><i class="fe fe-info"></i></span> <span
                                class="alert-inner--text"></span>
                            هنوز سوالی ثبت نشده است
                        </div>
                    @endforelse
                </div>
            </div>
            @can('create',\Modules\Core\Entities\Faq::class)
                <div class="card" id="create_edit_form">
                    <div class="card-header border-bottom"><h3 class="card-title">افزودن سوال برای اپلیکیشن</h3></div>
                    <div class="card-body">
                        <h5>
                            جهت افزودن سوال جدید فرم زیر را تکمیل کنید.
                        </h5>
                        <div class="pt-4">
                            <div class="form-group">
                                <input type="text"
                                       wire:model="question"
                                       class="form-control @error('question') is-invalid @enderror" id="name1"
                                       placeholder="متن سوال">
                                @error('question')
                                <div id="validationuserQuestion"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <textarea class="form-control @error('answer') is-invalid @enderror"
                                          name="example-textarea-input"
                                          wire:model="answer"
                                          rows="6"
                                          placeholder="متن پاسخ"></textarea>
                                @error('answer')
                                <div id="validationuserAnswer"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <a href="javascript:void(0)"
                               wire:loading.attr="disabled"
                               wire:loading.class="btn-loading btn-dark"
                               wire:loading.class.remove="btn-primary"
                               class="btn btn-primary"
                               wire:click="submit">ذخیره</a></div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{admin_asset('plugins/sweet-alert/sweetalert.min.js')}}"></script>
    <script src="{{admin_asset('plugins/notify/js/notifIt.js')}}"></script>

    <script>
        function showSuccessMessage($message) {
            notif({
                msg: $message,
                type: "primary",
                position: "bottom",
                bottom: '10',
                time: 500
            });
        }

        Livewire.on('success-saving', param => {
            showSuccessMessage(param.message);
        });

        Livewire.on('scroll-to-form', () => {
            $('html, body').animate({
                scrollTop: $("#create_edit_form").offset().top
            }, 300);
        });

        $('body').on('click', '.delete_confirm_alert', function (e) {
            e.preventDefault();
            let $label = $(this).data('label');
            let $id = $(this).data('id');
            $('body').removeClass('timer-alert');
            swal({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false,
                    title: "از حذف این مورد اطمینان دارید؟",
                    text: "آیا می‌خواهید " + $label + " را حذف کنید؟",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn btn-danger",
                    confirmButtonText: "بله حذف شود",
                    cancelButtonText: "خیر",
                    closeOnConfirm: true
                },
                function () {
                    Livewire.dispatch('delete', {model: $id});
                    //show loading animation

                });
        });

    </script>
@endpush
