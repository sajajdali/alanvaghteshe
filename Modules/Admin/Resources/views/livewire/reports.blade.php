<div>
    <div>
        @if($fromCache)
            <div class="alert alert-warning d-flex justify-content-between align-items-center">
                <div>
                    اطلاعات از <strong>کش</strong> لود شده است؛
                    ساخته‌شده در
                    <strong>{{ verta($cachedAt)->format('Y/m/d H:i') }}</strong>
                </div>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary"
                            wire:click="refreshReports" wire:loading.attr="disabled">
                        بروزرسانی از دیتابیس
                    </button>
                    <button class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('کش حذف شود و دوباره ساخته شود؟')"
                            wire:click="clearCache" wire:loading.attr="disabled">
                        حذف کش
                    </button>
                </div>
            </div>
        @else
            <div class="alert alert-success">
                اطلاعات تازه از دیتابیس لود شده است.
            </div>
        @endif

        {{-- ... بقیه‌ی کد فعلی شما (کارت‌ها و چارت‌ها) ... --}}
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body pb-2">
                    <div class="title-head mb-3 border-bottom">
                        <h3 class="mb-5 card-title fs-20">
                            <i class="fa fa-usd" aria-hidden="true"></i>
                            میزان فروش
                        </h3>
                    </div>
                    <div class="content-main row mt-5">
                        <div class="col-md-3">
                            <ul class="task-list1 row">
                                <li class="col-12">
                                    <span class="mb-0 fs-17 me-1">
                                        <i class="task-icon1 bg-primary me-3"></i>فروش کل: </span>
                                    <span class="mb-0 fs-17">{{ number_format($showData['sale']['total']) }}</span>
                                </li>
                                <li class="col-12">
                                    <span class="mb-0 fs-17 me-1">
                                        <i class="task-icon1 bg-secondary me-3"></i>فروش امروز: </span>
                                    <span class="mb-0 fs-17">{{ number_format($showData['sale']['today']) }}</span>
                                </li>
                                <li class="col-12">
                                    <span class="mb-0 fs-17 me-1">
                                        <i class="task-icon1 bg-warning me-3"></i>فروش دیروز: </span>
                                    <span class="mb-0 fs-17">{{ number_format($showData['sale']['yesterday']) }}</span>
                                </li>
                                <li class="col-12">
                                    <span class="mb-0 fs-17 me-1">
                                        <i class="task-icon1 bg-info me-3"></i>فروش هفته جاری: </span>
                                    <span class="mb-0 fs-17">{{ number_format($showData['sale']['week']) }}</span>
                                </li>
                                <li class="col-12">
                                    <span class="mb-0 fs-17 me-1">
                                        <i class="task-icon1 bg-primary me-3"></i>فروش ماه جاری: </span>
                                    <span class="mb-0 fs-17">{{ number_format($showData['sale']['month']) }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartDonut"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body pb-2">
                    <div class="title-head mb-3 border-bottom">
                        <h3 class="mb-5 card-title fs-20">
                            <i class="fa fa-user-o" aria-hidden="true"></i>
                            امار عضویت
                        </h3>
                    </div>
                    <div class="content-main row mt-5">
                        <div class="col-md-3">
                            <ul class="task-list1 row">
                                <li class="col-12">
                                    <span class="mb-0 fs-17 me-1">
                                        <i class="task-icon1 bg-primary me-3"></i>سه ماهه گذشته: </span>
                                    <span class="mb-0 fs-17">{{ number_format($showData['user']['last_three_months']) }}</span>
                                </li>
                                <li class="col-12">
                                    <span class="mb-0 fs-17 me-1">
                                        <i class="task-icon1 bg-secondary me-3"></i>ماه جاری: </span>
                                    <span class="mb-0 fs-17">{{ number_format($showData['user']['last_month']) }}</span>
                                </li>
                                <li class="col-12">
                                    <span class="mb-0 fs-17 me-1">
                                        <i class="task-icon1 bg-warning me-3"></i>هفته جاری: </span>
                                    <span class="mb-0 fs-17">{{ number_format($showData['user']['last_week']) }}</span>
                                </li>
                                <li class="col-12">
                                    <span class="mb-0 fs-17 me-1">
                                        <i class="task-icon1 bg-info me-3"></i>روز گذشته: </span>
                                    <span class="mb-0 fs-17">{{ number_format($showData['user']['yesterday']) }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <div class="chartjs-wrapper-demo">
                                    <canvas id="chartPolar" class="h-275"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body pb-2">
                    <div class="title-head mb-3 border-bottom">
                        <h3 class="mb-5 card-title fs-20">
                            <i class="fa fa-heartbeat" aria-hidden="true"></i>
                            تعداد رژیم دریافتی
                        </h3>
                    </div>
                    <div class="content-main row mt-5">
                        <div class="col-md-3">
                            <ul class="task-list1 row">
                                <li class="col-12">
                                    <span class="mb-0 fs-17 me-1">
                                        <i class="task-icon1 bg-primary me-3"></i>سه ماهه گذشته: </span>
                                    <span class="mb-0 fs-17">{{ number_format($showData['diets']['last_three_months']) }}</span>
                                </li>
                                <li class="col-12">
                                    <span class="mb-0 fs-17 me-1">
                                        <i class="task-icon1 bg-secondary me-3"></i>ماه جاری: </span>
                                    <span class="mb-0 fs-17">{{ number_format($showData['diets']['last_month']) }}</span>
                                </li>
                                <li class="col-12">
                                    <span class="mb-0 fs-17 me-1">
                                        <i class="task-icon1 bg-warning me-3"></i>هفته جاری: </span>
                                    <span class="mb-0 fs-17">{{ number_format($showData['diets']['last_week']) }}</span>
                                </li>
                                <li class="col-12">
                                    <span class="mb-0 fs-17 me-1">
                                        <i class="task-icon1 bg-warning me-3"></i>روز گذشته </span>
                                    <span class="mb-0 fs-17">{{ number_format($showData['diets']['yesterday']) }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chartBar1" class="h-275"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script src="{{ admin_asset('plugins/chart/Chart.bundle.js') }}"></script>
    <script src="{{ admin_asset('plugins/chart/utils.js') }}"></script>
    <script src="{{ admin_asset('js/chart.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Pie Chart
            const chartDatas = @json($showData);
            const datapie = {
                labels: ['ماه جاری', 'هفته جاری', 'دیروز', 'امروز'],
                datasets: [{
                    data: [
                        chartDatas.sale.month,
                        chartDatas.sale.week,
                        chartDatas.sale.yesterday,
                        chartDatas.sale.today,
                    ],
                    backgroundColor: ['#a4dfdf', '#9ad0f5', '#ffcf9f', '#ffb0c1'],
                }]
            };
            var optionpie = {
                maintainAspectRatio: false,
                responsive: true,
                legend: {
                    display: false,
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            };
            var ctx7 = document.getElementById('chartDonut');
            var myPieChart7 = new Chart(ctx7, {
                type: 'pie',
                data: datapie,
                options: optionpie
            });
            // bar chart
            var ctx = document.getElementById("chartBar1");
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [
                        "سه ماه گذشته",
                        "ماه جاری",
                        "هفته جاری",
                        "روز گذشته",
                    ],
                    datasets: [{
                        label: "تعداد رژیم دریافتی",
                        data: [
                            chartDatas.diets.last_three_months,
                            chartDatas.diets.last_month,
                            chartDatas.diets.last_week,
                            chartDatas.diets.yesterday,
                        ],
                        borderColor: "#ffb0c1",
                        borderWidth: "0",
                        backgroundColor: "#ffb0c1"
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        labels: {
                            fontColor: "#77778e"
                        },
                    },
                }
            });
            // polar chart
            var ctx = document.getElementById("chartPolar");
            var myChart = new Chart(ctx, {
                type: 'polarArea',
                data: {
                    datasets: [{
                        data: [
                            chartDatas.user.last_three_months,
                            chartDatas.user.last_month,
                            chartDatas.user.last_week,
                            chartDatas.user.yesterday,
                        ],
                        backgroundColor: ['#a4dfdf', '#9ad0f5', '#ffcf9f', '#ffb0c1'],
                        hoverBackgroundColor: ['#87d6d6', '#7fb6db', '#ffbf80',
                            '#ffdb85'
                        ],
                        borderColor: '#fff',
                    }],
                    labels: ["سه ماه گذشته", "ماه جاری", "هفته جاری", "روز گذشته"]
                },
                options: {
                    scale: {
                        gridLines: {
                            color: 'rgba(119, 119, 142, 0.2)'
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        labels: {
                            fontColor: "#77778e"
                        },
                    },
                }
            });
        });
    </script>
@endpush

@push('scripts')
    <script>
        window.addEventListener('reload-page', () => window.location.reload());
    </script>
@endpush
