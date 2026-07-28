<div>
    <div class="page-header mb-5">
        <div>
            <h1 class="page-title">
                <span>تنظیمات یادآوری ها</span>
            </h1>
        </div>
    </div>
    @include('admin::layouts.components.alert')
    <div class="card">
        <div class="card-body">
            <div class="row">
                {{-- send for --}}
                <div class="col-md-12">
                    <div class="form-group" wire:ignore>
                        <label class="form-label">
                            <strong>ارسال برای</strong>
                        </label>
                        <select class="form-control select2-show-search form-select" id="exerciseSelet" data-id='sendFor'
                            data-placeholder="انتخاب کنید..">
                            <option value="">انتخاب کنید</option>
                            @foreach (Modules\Reminder\Enum\ReminderTypeEnum::cases() as $type)
                                <option @if(isset($this->form['sendFor']) && $this->form['sendFor'] == $type) selected @endif value="{{ $type->value }}">{{ $type->getName() }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('form.sendFor')
                        <div class="text-danger">
                            <i class="fa fa-exclamation-triangle ms-1 mt-1" aria-hidden="true"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                @if($this->isDietOrExerciseReminder())
                     <div class="col-md-12 mb-3 mt-4">
                        <label for="">
                            ارسال برای جلسه</label>
                        <select class="form-control  form-select  @error('form.session_number') is-invalid @enderror"
                                wire:model='form.session_number' data-placeholder="انتخاب کنید...">
                            <option value="" label="تمامی جلسات"></option>
                            @for ($i = 1; $i < 29; $i++)
                                <option label="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        @error('form.send.time')
                        <div class="text-danger">
                            <i class="fa fa-exclamation-triangle ms-1 mt-1" aria-hidden="true"></i> {{ $message }}
                        </div>
                        @enderror
                    </div>
                @endif
                {{-- line seperator --}}
                <div class="col-12 col-md-3 mt-5  mt-md-5 mb-md-3">
                    <div
                        class="d-flex align-items-center @if ($errors->hasAny('form.smsTemplateName', 'form.notificationText')) text-danger @else text-primary @endif">
                        <i class="fa fa-flag  fa-2x mb-1 me-2" aria-hidden="true"></i>
                        <h4 class="mt-1">انتخاب نوع ارسال</h4>
                    </div>
                </div>
                <div class="col-12 col-md-9 mt-md-5  mt-md-5 mb-md-3">
                    <hr class="@if ($errors->hasAny('form.smsTemplateName', 'form.notificationText')) bg-danger @endif">
                </div>
                {{-- send Type --}}
                <div class="col-12 mt-4">
                    <div class="row">
                        @foreach (Modules\Reminder\Enum\ReminderSendTypeEnum::cases() as $key => $value)
                            <div class="col-md-4">
                                <label class="rdiobox" for="sendType{{ $value->getWireModelName() }}">
                                    <input name="sendNotifType" type="radio" class="radio-secondary sendType"
                                        wire:model='form.sendType' value="{{ $value }}"
                                        value="{{ $value->getWireModelName() }}"
                                        id="sendType{{ $value->getWireModelName() }}">
                                    <span>{{ $value->getName() }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-12 mt-4" id="smsTemplateDiv" wire:ignore.self>
                    <label for="smsTemplate" class="form-label">نام قالب پیامکی</label>
                    <input wire:model='form.smsTemplateName'
                        class="form-control @error('form.smsTemplateName') is-invalid @enderror" id="smsTemplate"
                        placeholder="نام قالب پیامکی که در پنل پیامکی ثبت کردید" type="text">
                    @error('form.smsTemplateName')
                        <div class="text-danger">
                            <i class="fa fa-exclamation-triangle ms-1 mt-1" aria-hidden="true"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-12 d-none" id="notificationTemplateDiv" wire:ignore.self>
                    <label for="validationTextarea" class="form-label">متن اعلان</label>
                    <textarea wire:model='form.notificationText' class="form-control" id="validationTextarea"
                        placeholder="متن اعلان را وارد کنید"></textarea>
                    <small class="text-gray">برای استفاده از متن متغیر، از %param1% استفاده کنید!</small>
                    @error('form.notificationText')
                        <div class="text-danger">
                            <i class="fa fa-exclamation-triangle ms-1 mt-1" aria-hidden="true"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
                {{-- params --}}
                <div id="paramDiv" class="col-12 row">
                    <div class="col-12 col-md-3 mt-5  mt-md-5 mb-md-3">
                        <div class="d-flex align-items-center text-primary">
                            <i class="fa fa-random  fa-2x mb-1 me-2" aria-hidden="true"></i>
                            <h4 class="mt-1">انتخاب پارامتر ها</h4>
                        </div>
                    </div>
                    <div class="col-12 col-md-9 mt-md-5  mt-md-5 mb-md-3">
                        <hr>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">انتخاب پارامتر</label>
                            <div class="row">
                                <div class="col-1">
                                    <span class="badge bg-secondary rounded-phill mt-md-1">1</span>
                                </div>
                                <div class="col-11">
                                    <select class="form-control  form-select" wire:model='form.parametr.0'
                                        data-id="1" wire:ignore.self data-placeholder="انتخاب کنید...">
                                        <option label="انتخاب کنید..."></option>
                                        @foreach (Modules\Reminder\Enum\ReminderParametersEnum::cases() as $parameter)
                                            <option value="{{ $parameter }}">{{ $parameter->getName() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    @for ($i = 0; $i < $fetchData['parametrCounter']; $i++)
                        <div class="col-12 mt-2">
                            <div class="row w-100">
                                <div class="col-12 col-md-1 mb-2 mb-md-0">
                                    <span class="badge bg-secondary rounded-phill mt-md-1">{{ $i + 2 }}</span>
                                </div>
                                <div class="col-9">
                                    <div class="form-group">
                                        <select class="form-control form-select "
                                            wire:model='form.parametr.{{ $i + 1 }}'
                                            data-id="{{ $i }}" wire:ignore.self
                                            data-placeholder="انتخاب کنید...">
                                            <option label="انتخاب کنید..."></option>
                                            @foreach (Modules\Reminder\Enum\ReminderParametersEnum::cases() as $parameter)
                                                <option @if (isset($this->form['parametr']) && in_array($parameter->value, $this->form['parametr'])) selected @endif
                                                    value="{{ $parameter->value }}">{{ $parameter->getName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-2  mb-3 mb-md-0 text-end">
                                    <button class="btn btn-danger " wire:loading.class='btn-loading btn-gray'
                                        wire:target='removeParam({{ $i }})'
                                        wire:click='removeParam({{ $i }})'>
                                        <span class="d-md-none"><i class="fa fa-times" aria-hidden="true"></i></span>
                                        <span class=" d-none d-md-block">حذف پارامتر</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endfor
                    <div class="col-md-12">
                        <button wire:click='addMoreParam' wire:loading.class='btn-loading bg-gray'
                            class="btn btn-light">افزودن پارامتر</button>
                    </div>
                </div>
                {{-- line seperator --}}
                <div class="col-12 col-md-3 mt-5  mt-md-5 mb-md-3">
                    <div
                        class="d-flex align-items-center   @error('form.send.time') text-danger @else text-primary @enderror">
                        <i class="fa fa-clock-o fa-2x mb-1 me-2" aria-hidden="true"></i>
                        <h4 class="mt-1">زمان ارسال</h4>
                    </div>
                </div>
                <div class="col-12 col-md-9 mt-md-5  mt-md-5 mb-md-3">
                    <hr class="@error('form.send.time') bg-danger @enderror">
                </div>

                {{-- line seperator send Time --}}
                <div class="col-12 mt-4 mt-4">
                    <label for="smsTemplate" class="form-label">چند روز بعد</label>
                    <input wire:model='form.send.day'
                        class="form-control @error('form.send.day') is-invalid @enderror" id="smsTemplate"
                        placeholder="چند روز " type="text">
                    <small class=" ms-2">به عدد وارد کنید !</small>
                    <small class="text-gray ms-2">
                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                        در صورتی که  برای پکیج ها، عدد روز ارسال منفی باشد ، روز ارسال ، با کسر این عدد از زمان انتهای پکیج محاسبه میشود
                    </small>
                    @error('form.send.day')
                        <div class="text-danger">
                            <i class="fa fa-exclamation-triangle ms-1 mt-1" aria-hidden="true"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- activation --}}
                <div class="col-md-12 mt-3">
                    <div class="main-toggle-group d-flex align-items-center ms-0">
                        <div class="toggle toggle-lg toggle-primary my-1 customCheckbox on" wire:ignore.self
                            data-id="visitType.inPerson">
                            <span></span>
                        </div>
                        <div class="ms-2">
                            <p class="text-muted m-0">فعال بودن یادآور</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 opacity-50">
                    <hr>
                </div>
                @error('*')
                <div class="col-md-12 alert alert-danger fade show" role="alert">
                    لطفا خطا های بالا را برطرف کنید!
                </div>
                @enderror
                <div class="col-md-12 text-end">
                    <button wire:click='storeReminder' wire:loading.class='btn-loading bg-gray'
                        wire:target='storeReminder' class="btn btn-success"><strong>ذخیره ی یادآور</strong></button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script src="{{ admin_asset('plugins/select2/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.select2-show-search').select2();
            Livewire.on('loadjs', function() {
                setTimeout(() => {
                    $('.select2-show-search').select2();
                }, 200);
            });
            $('#numberOfBeforeVisitDate_input').change(function() {
                @this.set('form.send_at_specific_date', $(this).val());
            });
            $('.customCheckbox').on('click', function() {
                if ($(this).hasClass('on')) {
                    @this.set('form.active', 1);
                } else {
                    @this.set('form.active', 0);
                }
            });
            $('.docradio').on('click', function(e) {
                if ($('#rdio-secondary-unchecked').is(':checked')) {
                    $('#selectDoctorSelectBox').fadeIn('d-none');
                    $('#selectDoctorSelectBox').removeClass('d-none');
                } else {
                    $('#selectDoctorSelectBox').fadeOut('d-none');
                    $('#selectDoctorSelectBox').addClass('d-none');
                }
            });
            $('.sendType').on('change', function(e) {
                var val = $(this).val();
                changeSendTypeStatus(val);
            });
            if ({{ isset($form['sendType']) }}) {
                val = "{{ $form['sendType'] }}";
                changeSendTypeStatus(val);
            };

            function changeSendTypeStatus(val) {
                if (val == '1') {
                    $('#smsTemplateDiv').fadeIn().removeClass('d-none');
                    $('#notificationTemplateDiv').fadeOut().addClass('d-none');
                    $('#paramDiv').fadeIn().removeClass('d-none');
                } else if (val == '2' || val == '3') {
                    $('#notificationTemplateDiv').fadeIn().removeClass('d-none');
                    $('#smsTemplateDiv').fadeOut().addClass('d-none');
                    $('#paramDiv').fadeIn().removeClass('d-none');
                } else {
                    $('#smsTemplateDiv, #notificationTemplateDiv').addClass('d-none');
                    $('#paramDiv').fadeOut().addClass('d-none');
                }
            }
            $('.dayInput').on('click', function() {
                if ($('#numberOfBeforeVisitDate_radio').prop('checked')) {
                    $('#numberOfBeforeVisitDate_input').prop('disabled', false);
                } else {
                    $('#numberOfBeforeVisitDate_input').prop('disabled', true);
                }
            });
            $('#exerciseSelet').on('change', function() {
                @this.set('form.sendFor', $(this).val());
            });
            $('#speciificDocSelect2').on('change', function() {
                @this.set('form.specificDoctors', $(this).val());
            });
        });
    </script>
@endpush
