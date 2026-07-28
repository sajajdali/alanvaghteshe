<div>
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card">
                <div class="card-header border-bottom d-flex justify-content-between">
                    <h3 class="card-title">رژیم های کابر</h3>
                    @if (isset($user->gender) && $user->tall && $user->weight && isset($user->targetPlan) && $user->age() )
                        <a href="{{ route('admin.user.assign.diet', [$user]) }}" class="btn btn-success shadow">تجویز
                            رژیم</a>
                    @else
                        <button class="btn btn-success" wire:click='compelete'>تجویز رژیم</button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border text-nowrap text-md-nowrap table-striped text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>رژیم</th>
                                <th>وضعیت</th>
                                <th>تاریخ شروع</th>
                                <th>تاریخ پایان</th>
                                <th>فعال بودن رژیم</th>
                                <th>تاریخ درخواست رژیم</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($userDiets->isNotEmpty())
                                @foreach ($userDiets as $index => $userDiet)
                                    <tr>
                                        <td>{{ $userDiet->id }}</td>
                                        <td><a href="{{ route('admin.presCribed-diets.detail', $userDiet->id) }}">
                                                {{ $userDiet->dietPlan?->name ?? 'اختصاص داده نشده' }} </a></td>
                                        <td>{!! $userDiet->status->getName() !!}</td>
                                        <td>
                                            @if ($userDiet->start_date)
                                                {{ verta($userDiet->start_date)->format('Y/m/d') }}
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($userDiet->start_date)
                                                {{ verta($userDiet->end_date)->format('Y/m/d') }}
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                        <td>{!! $userDiet->activeBadge() !!}</td>
                                        <td>{{ verta($userDiet->created_at)->format('Y/m/d') }}</td>
                                        <td>
                                            <div class="btn-group mt-2 mb-2">
                                                <button type="button" class="btn btn-primary dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                    عملیات <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li>
                                                        <a
                                                            href="{{ route('admin.presCribed-diets.detail', $userDiet->id) }}">مشاهده</a>
                                                    </li>
                                                    <li><a wire:click='removeDiet({{ $userDiet->id }})'
                                                           wire:confirm='آیا از حذف این رژیم مطمعن هستید؟'
                                                           href="#">حذف</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8">
                                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                                            <span class="alert-inner--text">رژیمی یافت نشد!</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
