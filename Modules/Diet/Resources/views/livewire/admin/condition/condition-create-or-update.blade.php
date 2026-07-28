<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">
                {{ $isEdited ? 'ویرایش شرایط' : 'افزودن شرایط جدید' }}
            </h1>
        </div>
    </div>

    <a href="/admin/food" wire:navigate>Profile</a>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">
                        {{ $isEdited ? 'ویرایش شرایط' : 'افزودن شرایط جدید' }}
                    </h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent>
                        <div class="form-row">
                            <div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 mb-3">
                                <label for="condition">نام شرط (الزامی)</label>
                                <select wire:model.live="form.conditionKey" name="condition"
                                        class="form-control form-select @error('form.conditionKey') is-invalid @enderror"
                                        id="default-dropdown" data-bs-placeholder="انتخاب شرط">
                                    @foreach(\Modules\Diet\Enum\ConditionKeyEnum::cases() as $enum)
                                        <option value="{{$enum->value}}">{{$enum->getName()}}</option>
                                    @endforeach
                                </select>
                                @error('form.conditionKey')
                                <div id="validationName"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 mb-3">

                                <div class="@error('form.disease') is-invalid @enderror">
                                    <div class="form-group">
                                        <p class="card-sub-title">این شرط برای چه جایی اعمال شود؟</p>
                                        <div class="selectgroup selectgroup-pills">
                                            @foreach(\Modules\Diet\Enum\ConditionApplyItemEnum::cases() as $enum)
                                                <label class="selectgroup-item">
                                                    <input type="checkbox" name="form.applyTo[]"
                                                           value="{{$enum->value}}"
                                                           @if(in_array($enum->value , $form['applyTo'])) checked
                                                           @endif class="selectgroup-input"
                                                           wire:model="form.applyTo">
                                                    <span class="selectgroup-button">{{$enum->getName()}}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @error('form.applyTo')
                                <div id="validationName"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        @if($form['conditionKey'] != \Modules\Diet\Enum\ConditionKeyEnum::DISEASE->value)
                            <div class="form-row">
                                <div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <label for="condition">لیست فیلد های این شرط</label>
                                    <div class="example">
                                        <div id="wrap_parameters">
                                            @for($i = 0 ; $i < $form['countItems'] ; $i++ )
                                                <div class="row param_items mt-2">
                                                    <div class="col-6 col-md-8">
                                                        <input type="text" wire:model="form.items.{{$i}}"
                                                               class="form-control mb-4 mb-md-0 "
                                                               value=""
                                                               inputmode="text">
                                                    </div>
                                                    <div class="col-6 col-md-2">
                                                    <span class="ml-2" style="margin-left: 25px">
                                                        <label>
                                                            <input type="checkbox" value="check_{{$i}}"
                                                                   data-checkboxes="mygroup"
                                                                   wire:model="form.checked.{{$i}}"
                                                                   class="">
                                                            پرباشد
                                                        </label>
                                                    </span>
                                                        <button type="button" wire:click="deleteItem({{$i}})"
                                                                class="btn btn-danger ps-3">حذف
                                                        </button>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                        @error('form.items')
                                        <div id="validationName"
                                             class="invalid-feedback d-block">{{ $message }}</div>@enderror

                                        <div class="mt-2 mb-2">
                                            <button wire:click="addItems" type="button" class="btn btn-secondary">اضافه
                                                کردن
                                                ایتم جدید
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @else
                            <div class="form-row">
                                <div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <div class="alert alert-info" role="alert">
                                        <span class="alert-inner--icon me-2"><i class="fe fe-bell"></i></span>
                                        <span class="alert-inner--text">لیست بیماری ها از طریق دیتابیس قابل مدیریت است و امکان تغییر از این قسمت را ندارید</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <button type="submit" class="btn btn-primary" wire:loading.class="bg-gray btn-loading disabled"
                                wire:click="updateOrCreate">
                            @if($isEdited)
                                ویرایش شرایط
                            @else
                                ایجاد شرایط
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
    <script>
        (function ($) {

            $(document).on("click", ".remove-param", function () {

                if ($(".param_items").length > 1) {
                    $(this).closest('.param_items').remove();
                } else {
                    if ($(".param_items").length == 1) {
                        $('.will_learn_input').val('');
                    }
                }
            });

            $(document).on("click", "#add_requirement", function () {
                let slector = $('.param_items:nth-child(1)').clone();
                slector.find('.will_learn_input').val('');
                slector.appendTo('#wrap_parameters');
            });

        })(jQuery);

    </script>
@endpush
