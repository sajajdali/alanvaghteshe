<div>
    <div class="page-header">
        @if (isset($dietErrors) && count($dietErrors) > 0)
            <button class="btn btn-danger" onclick="" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                نمایش خطا ها ({{ count($dietErrors['errors']) }})
            </button>
        @endif
        <div class="ms-auto pageheader-btn">

            @if($dietRequest->dietplan)

                    <a href="#" wire:click="reGenerateAndDeleteOldData"
                       wire:loading.class="bg-gray btn-loading disabled"
                       wire:confirm='کنید؟ میخواهید این رژیم را پاک  و مجدد تجویز ' class="btn btn-azure">پاک کردن
                        و تجویز مجدد</a>

{{--                @can('create', \Modules\Diet\Entities\detail::class)--}}
                    <a href="#" wire:click='sendSmsGenerateDiet'
                       wire:loading.class="bg-gray btn-loading disabled"
                       wire:confirm='آیا میخواهید پیامک تجویز رژیم برای کاربر ارسال شود '
                       class="btn btn-warning">ارسال پیامک رژیم</a>
{{--                @endcan--}}
            @endif

        </div>
    </div>
    @include('admin::layouts.components.alert')
    @if (isset($dietErrors) && count($dietErrors) > 0)
        <div class="collapse" id="collapseExample">
            <div class="card card-body">
                <div class="collapse" id="collapseExample">
                    <div class="card card-body">
                        <div class="row">
                            @if($dietErrors['error_type'] == 'request_detail')
                                @foreach ($dietErrors['errors'] as $key => $errors)
                                    <table class="table border text-nowrap text-md-nowrap table-striped">
                                        <tbody>
                                        <tr>
                                            <th class="bg-white text-center align-middle border-end" rowspan="2">
                                                {{ $key + 1 }}
                                            </th>
                                            <th>متن پیام</th>
                                            <th>برای روز</th>
                                            <th>برای وعده ی</th>
                                        </tr>
                                        <tr>
                                            <td>{{ $errors['error'] }}</td>
                                            <td>{{ $errors['day'] }}</td>
                                            <td>{{ $errors['meal']['name'] }}</td>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <span></span>
                                @endforeach
                            @elseif($dietErrors['error_type'] == 'request_plan')
                                <table class="table border text-nowrap text-md-nowrap table-striped">
                                    <tbody>
                                    <tr>

                                        <th>نوع اخطار</th>
                                    </tr>
                                    <tr>
                                        <td>{{ $dietErrors['errors'][0]['error'] ?? '' }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (!$dietRequest->dietPlan)

        <div class="card">
            <div class="card-body">
                <div class="alert alert-danger alert-dismissible fade show" role="alert"> <span
                        class="alert-inner--text">برای
                        هیچ پلنی با توجه به مشخصات کاربر برای وی یافت نشد!!!</span>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <h4 class="mb-2">اختصاص پلن رژیم</h4>
                <div class="col-md-12 mt-4 my-2">
                    <select wire:model='selectedDietPlan'
                            class="form-select @error('selectedDietPlan') is-invalid @enderror" id="validationCustom04"
                            required="">
                        <option selected="" disabled="" value="">انتخاب کنید...</option>
                        @foreach ($dietPlans as $key => $dietplan)
                            <option value="{{ $dietplan->id }}">{{ $dietplan->name }}</option>
                        @endforeach
                    </select>
                    @error('selectedDietPlan')
                    <div id="validationName"
                         class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <button wire:click='assignDietPlan' class="btn btn-success mt-3">
                    <span wire:loading.remove wire:target='assignDietPlan'>تخصیص</span>
                    <div wire:loading wire:target='assignDietPlan' class="spinner-border spinner-border-sm text-primary"
                         role="status">
                    </div>
                </button>
            </div>
        </div>
    @endif

    <div class="row mt-4" id="mainTab">
        <div class="card pt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="wideget-user-tab">
                        <div class="tab-menu-heading">
                            <div class="tabs-menu1" wire:ignore>
                                <ul class="nav">
                                    <li>
                                        <a href="#prescribedDetail" class="active show " data-bs-toggle="tab">رژیم تجویز
                                            شده</a>
                                    </li>
                                    <li>
                                        <a href="#userDetail" data-bs-toggle="tab">اطلاعات کاربری</a>
                                    </li>
                                    <li>
                                        <a href="#usercondition" data-bs-toggle="tab">  شرایط هنگام دریافت رژیم</a>
                                    </li>
                                    <li>
                                        <a href="#diet" data-bs-toggle="tab"> جزئیات رژیم تجویز شده</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-content">
                    <div wire:ignore.self class="tab-pane active show" id='prescribedDetail'>
                        <livewire:diet::admin.prescribed-diets.details.prescription-diet-detail
                            :dietReqestId="$dietReqestId"/>
                    </div>
                    <div wire:ignore.self class="tab-pane" id='userDetail'>
                        <livewire:diet::admin.prescribed-diets.details.user-detail :dietReqestId="$dietReqestId"/>
                    </div>
                    <div wire:ignore.self class="tab-pane" id='usercondition'>
                        <livewire:diet::admin.prescribed-diets.details.user-conditions :dietReqestId="$dietReqestId"/>
                    </div>
                    <div wire:ignore.self class="tab-pane" id='diet'>
                        <livewire:diet::admin.prescribed-diets.details.diet :dietRequest="$dietRequest"/>
                    </div>
                </div>
            </div><!-- COL-END -->
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ admin_asset('plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ admin_asset('plugins/sweet-alert/admin.sweetalert.js') }}"></script>

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
@endpush
