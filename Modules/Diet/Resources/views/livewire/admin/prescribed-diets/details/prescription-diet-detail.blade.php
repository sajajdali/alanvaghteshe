<div>
    <div class="row mt-4" id="rejims">
        <div class="card pt-3">
            <div class="col-lg-12">
                @if (!empty($this->details))
                    @if(isset($successFully))
                        <div class="alert alert-success mt-4 alert-dismissible fade show" role="alert">
                            <span class="alert-inner--text">{{$successFully}}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    @endif
                    <h4 class="mb-5 mt-3">رژیم ثبت شده</h4>
                    <table class="table text-nowrap text-md-nowrap table-bordered text-center"
                           wire:loading.class="op-0-3">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">نام رژیم</th>
                            <th scope="col">وضعیت</th>
                            <th scope="col">میزان کالری</th>
                            <th scope="col">تاریخ شروع</th>
                            <th scope="col">تاریخ اتمام</th>
                            <th scope="col">تاریخ درخواست</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if ($dietReqeust->exists())
                            <tr>
                                <td class="text-center">{{ $dietReqeust->id }}</td>
                                <td class="text-center">{!! $dietReqeust->dietPlan?->name ?? ' <span class="rounded-pill bg-warning p-1">یافت نشد</span>' !!} </td>
                                <td>
                                    {!! $dietReqeust->status->getName() !!}
                                </td>
                                <td>
                                    {{ number_format($dietReqeust->calories) }}
                                    @if(!$changeCalorie)
                                        <button type="button"
                                                class="btn btn-sm btn-cyan"
                                                wire:click="toggleChangeCalorie"
                                                wire:loading.class="btn-loading"
                                                wire:loading.attr="disabled">
                                            تغییر
                                        </button>
                                        <br>
                                        <br>
                                        <span class=" tag tag-light"> کالری پایه :{{number_format($dietReqeust->detail['caloriesAndUnits']['base_calorie'])}}</span>
                                    @else
                                        <div class="alert alert-info mt-3">
                                            <div class="form-group">
                                                <label class="form-label" for="default-dropdown">کالری مورد نظر
                                                    شما</label>
                                                <input type="number"
                                                       class="form-control @error('form.new_calorie') is-invalid @enderror"
                                                       id="fasting_name"
                                                       wire:model="form.new_calorie" placeholder="کالری جدید">
                                                @error('form.new_calorie')
                                                <div style="display: block"
                                                     class="invalid-feedback">   {{ $message }}  </div> @enderror
                                            </div>
                                            <div class="form-group">
                                                <button type="button"
                                                        class="btn btn-success "
                                                        wire:click="changeCalorieAction"
                                                        wire:loading.class="btn-loading"
                                                        wire:loading.attr="disabled"
                                                >
                                                    تغییر کالری
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    {{ verta($dietReqeust->start_at)->format('Y/m/d') }}
                                </td>
                                <td>
                                    {{ verta($dietReqeust->end_date)->format('Y/m/d') }}
                                </td>
                                <td>
                                    {{ verta($dietReqeust->created_at)->format('Y/m/d ساعت H:i') }}
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    <div class="col-md-12 mt-5">
                        <div class="card-header flex-column align-items-start">
                            <h5>وضعیت پیشرفت رژیم</h5>
                            <hr class="w-100">
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="chart-container">
                                        <canvas id="chartPie" class="h-275"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h4 class="mb-5 mt-3">جزئیات آمار</h4>
                                    {{-- <div class="vr h-75" style="opacity:0.1"></div> --}}
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table border text-nowrap text-md-nowrap table-striped">

                                                <tbody>
                                                <tr>
                                                    <td>طول دوره</td>
                                                    <td>{{ $totaldays }} دوره</td>
                                                </tr>
                                                <tr>
                                                    <td>تعداد وعده ها</td>
                                                    <td>{{ $totallMeals }}</td>
                                                </tr>
                                                <tr>
                                                    <td>درصد وعده های مصرف شده</td>
                                                    <td>{{ $donedlMeals }}</td>
                                                </tr>
                                                {{--                                                <tr>--}}
                                                {{--                                                    <td>درصد وعده های مانده</td>--}}
                                                {{--                                                    <td>{{ $leftToConsume }}</td>--}}
                                                {{--                                                </tr>--}}

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (array_key_exists('handwritten', $dietReqeust->detail))
                        <hr class="mt-5 mb-3" style="opacity: 0.5">
                        <h4 class="mt-4 mb-5">یادداشت های مربط با وعده ها</h4>
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap text-md-nowrap table-bordered text-center"
                                   wire:loading.class="op-0-3">
                                <thead>
                                <tr class="text-center">
                                    <th scope="col">وعده</th>
                                    <th scope="col">عنوان</th>
                                    <th scope="col">توضیحات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($dietReqeust->detail['handwritten'] as $meal_id => $handWritten)
                                    <tr>
                                        <td>{{ Modules\Diet\Entities\Meal::find($meal_id)?->name }}</td>
                                        <td>{{ $handWritten['title'] }}</td>
                                        <td>{{ $handWritten['body'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    <hr class="mt-5 mb-3" style="opacity: 0.5">
                    <h4 class="mt-4 mb-5">مشخصات تغذیه ای هر وعده</h4>
                    <div class="card">
                        <div class="wideget-user-tab">
                            <div class="tab-menu-heading">
                                <div class="tabs-menu1" wire:ignore>
                                    <ul class="nav">
                                        @foreach ($this->details as $key => $detailItems)
                                            <li><a href="#rejimDetail_{{ $key }}"
                                                   class="@if ($loop->first) active show @endif"
                                                   data-bs-toggle="tab">
                                                    {{ Modules\Diet\Enum\MainNutritionEnum::tryFrom($key)->getName() }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content">
                        @foreach ($this->details as $key => $detailItems)
                            <div wire:ignore.self class="tab-pane  @if ($loop->first) active show @endif"
                                 id={{ "rejimDetail_{$key}" }}>
                                <div class="card">
                                    <div class="card-body p-0">
                                        <div class="row row-sm">
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="mb-5 collapse" id="advanceSearch">
                                                        </div>
                                                        <div class="table-responsive mb-3">
                                                            <table class="table text-nowrap text-md-nowrap table-bordered"
                                                                   wire:loading.class="op-0-3">
                                                                <thead>
                                                                <tr class="text-center">
                                                                    <th scope="col">#</th>
                                                                    <th scope="col">روز</th>
                                                                    <th scope="col">تاریخ روز</th>
                                                                    <th scope="col">وعده</th>
                                                                    <th scope="col">نام وعده غذایی</th>
                                                                    <th scope="col">P</th>
                                                                    <th scope="col">C</th>
                                                                    <th scope="col">FAT</th>
                                                                    <th scope="col">Fib</th>
                                                                    <th scope="col">Cal</th>
                                                                    <th scope="col">وضعیت</th>
                                                                </tr>
                                                                </thead>

                                                                @php
                                                                    $currentDay = null;
                                                                    $totalProtein = 0;
                                                                    $totalCarb = 0;
                                                                    $totalFat = 0;
                                                                    $totalFiber = 0;
                                                                    $totalCalories = 0;
                                                                @endphp

                                                                @if ($detailItems)
                                                                    @foreach ($detailItems as $index => $detail)
                                                                        @if ($currentDay !== $detail->day_number)
                                                                            @if ($currentDay !== null)
                                                                                <tr class="fw-bold bg-light">
                                                                                    <td colspan="5" class="text-center">
                                                                                        جمع روز {{ $currentDay }}</td>
                                                                                    <td>{{ number_format($totalProtein) }}</td>
                                                                                    <td>{{ number_format($totalCarb) }}</td>
                                                                                    <td>{{ number_format($totalFat) }}</td>
                                                                                    <td>{{ number_format($totalFiber) }}</td>
                                                                                    <td>{{ number_format($totalCalories) }}</td>
                                                                                    <td></td>
                                                                                </tr>

                                                                                @php
                                                                                    $totalProtein = 0;
                                                                                    $totalCarb = 0;
                                                                                    $totalFat = 0;
                                                                                    $totalFiber = 0;
                                                                                    $totalCalories = 0;
                                                                                @endphp
                                                                            @endif

                                                                            @php $currentDay = $detail->day_number; @endphp
                                                                        @endif

                                                                            @php
                                                                                if ($detail->replaced_parent_id == null) {
                                                                                    $totalProtein += $detail->protein;
                                                                                    $totalCarb += $detail->carb;
                                                                                    $totalFat += $detail->fat;
                                                                                    $totalFiber += $detail->fiber;
                                                                                    $totalCalories += $detail->calories;
                                                                                } else {
                                                                                    $parentDetail = $this->parentDietdetail($detail);
                                                                                    if ($parentDetail) {
                                                                                        $totalProtein += $parentDetail->protein;
                                                                                        $totalCarb += $parentDetail->carb;
                                                                                        $totalFat += $parentDetail->fat;
                                                                                        $totalFiber += $parentDetail->fiber;
                                                                                        $totalCalories += $parentDetail->calories;
                                                                                    }
                                                                                }
                                                                            @endphp
                                                                        <tbody>
                                                                        <tr @if(! $this->isReplaced($detail)) style="background-color: {{ $color[$this->caculateColorAndParent($detail) - 1] }};"
                                                                            @else style="opacity: 0.3;" @endif>
                                                                            <td class="text-center">{{ $detail->id }}</td>
                                                                            <td class="text-center">{{ $detail->day_number }}</td>
                                                                            <td>{{ verta($detail->date_of_day)->format('Y/m/d') }}</td>
                                                                            <td>
                                                                                @if($detail->foodable)
                                                                                    <a href="{{ route('admin.food.edit', $detail->foodable->id) }}">
                                                                                        {{ app(\Modules\Diet\Service\DietService::class)->handleMealName($dietReqeust, $detail->meal) }}
                                                                                    </a>
                                                                                @else
                                                                                    —
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if ($foodTypeIsComined)
                                                                                    @foreach ($this->complexFoodResult($detail) as $items)
                                                                                        {{ $items }} </br>
                                                                                @endforeach
                                                                                @else
                                                                                    @unless($key == \Modules\Diet\Enum\MainNutritionEnum::special->value)
                                                                                        {{ $detail->number_of_unit }} {{ $detail->foodClass->foodUnit?->name ?? '-' }}
                                                                                    @endunless
                                                                                    {{ $detail->foodClass?->name ?? '-' }}
                                                                                @endif
                                                                            </td>

                                                                            <td>{{ $detail->protein }}</td>
                                                                            <td>{{ $detail->carb }}</td>
                                                                            <td>{{ $detail->fat }}</td>
                                                                            <td>{{ $detail->fiber }}</td>
                                                                            <td>{{ $detail->calories }}</td>
                                                                            <td class="text-center">
                                                                                {!! $detail->is_done != 0
                                                                                    ? '<i class="fa fa-check text-success fs-5" data-bs-toggle="tooltip" title="صرف شده"></i>'
                                                                                    : '<i class="fa fa-minus text-muted fs-5" data-bs-toggle="tooltip" title="صرف نشده"></i>' !!}
                                                                            </td>
                                                                        </tr>

                                                                        @if ($this->isReplaced($detail))
                                                                            @include('diet::livewire.admin.prescribed-diets.details.table-row-for-presciption-diet-detail', ['detail_parent' => $this->parentDietdetail($detail)])
                                                                        @endif
                                                                        </tbody>
                                                                    @endforeach

                                                                    {{-- جمع روز آخر --}}
                                                                    <tr class="fw-bold bg-light">
                                                                        <td colspan="5" class="text-center">جمع
                                                                            روز {{ $currentDay }}</td>
                                                                        <td>{{ number_format($totalProtein) }}</td>
                                                                        <td>{{ number_format($totalCarb) }}</td>
                                                                        <td>{{ number_format($totalFat) }}</td>
                                                                        <td>{{ number_format($totalFiber) }}</td>
                                                                        <td>{{ number_format($totalCalories) }}</td>
                                                                        <td></td>
                                                                    </tr>
                                                                @else
                                                                    <tr>
                                                                        <td colspan="11" class="text-center">
                                                                            <div class="alert alert-info">خطا در نمایش
                                                                                جزئیات!
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </table>
                                                        </div>
                                                        <div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script src="{{ asset('assets/admin/plugins/chart/Chart.bundle.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/chart/utils.js') }}"></script>
    <script>
        // Pie Chart
        var datapie = {
            labels: ['وعده های مانده', 'وعده های مصرف شده'],
            datasets: [{
                data: [{{ $totallMeals }}, {{ $donedlMeals }}],
                backgroundColor: ['#ffcf9f', '#ffb0c1', '#ffe6aa']
            }]
        };
        var optionpie = {
            maintainAspectRatio: false,
            responsive: true,
            legend: {
                display: false
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        };
        // Doughbut Chart
        var ctx6 = document.getElementById('chartPie');
        var myPieChart6 = new Chart(ctx6, {
            type: 'doughnut',
            data: datapie,
            options: optionpie
        });
        // using transparency
        var ctx3 = document.getElementById('chartBar3').getContext('2d');
    </script>
@endpush
