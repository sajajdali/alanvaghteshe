<div>

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card">
                <div class="card-header border-bottom d-flex justify-content-between">
                    <h3 class="card-title">دوره های ورزشی کاربر</h3>
                    <span class="badge bg-danger">ورزش غیر فعال است</span>
                    <button wire:click="assignExercise"
                            wire:confirm="دوره ورزشی با توجه به اخرین اطلاعاتی که برای کاربر درج شده است تجویز میشود. \n آیا از تجویز اطمینان دارید؟ "
                            href="{{route('admin.user.assign.package',[$user])}}"
                            class="btn btn-success shadow">اختصاص دوره
                        جدید
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border text-nowrap text-md-nowrap table-striped text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>نام برنامه</th>
                                <th>تعداد جلسه</th>
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($userExercise->isNotEmpty())
                                @foreach ($userExercise as $index => $exercise)
                                    <tr>
                                        <td class="text-center">{{ $exercise->id }}</td>
                                        <td>
                                            @if($exercise->user)
                                                {{ $exercise->user->full_name }}
                                            @else
                                                <span class="badge text-danger">
                                                کاربر یافت نشد
                                            </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($exercise->exercisePlanStrategy)
                                                {{ $exercise->exercisePlanStrategy->name }}
                                            @else
                                                <span class="badge text-danger">
                                                برنامه یافت نشد
                                            </span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $exercise->session_count }}
                                        </td>
                                        <td>
                                            {!! $exercise->status->getAdminBadge() !!}
                                        </td>
                                        <td>
                                            @canany(['update','delete'],$exercise)
                                                <div class="btn-group mt-2 mb-2">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        عملیات <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        @can('update',$exercise)
                                                            <li>
                                                                <a href="{{ route('admin.exercise_plan_request.edit',$exercise) }}">مشاهده
                                                                    برنامه</a>
                                                            </li>
                                                        @endcan
                                                        @can('delete',$exercise)
                                                            <li>
                                                                <a wire:click="removeExercise({{$exercise->id}})"
                                                                   wire:confirm="آیا از حذف اطمینان دارید ؟"
                                                                   data-label="برنامه"
                                                                   data-id="{{ $exercise->id }}"
                                                                   href="#">حذف
                                                                </a>
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
                                    <td colspan="7">
                                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                                            <span class="alert-inner--text">بسته ای یافت نشد!</span>
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
