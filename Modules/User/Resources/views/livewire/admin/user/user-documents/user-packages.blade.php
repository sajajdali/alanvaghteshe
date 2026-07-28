<div>

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card">
                <div class="card-header border-bottom d-flex justify-content-between">
                    <h3 class="card-title">بسته های کاربر</h3>
                    <a href="{{route('admin.user.assign.package',[$user])}}" class="btn btn-success shadow">اختصاص بسته جدید</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border text-nowrap text-md-nowrap table-striped text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>نام بسته</th>
                                    <th>نوع</th>
                                    <th>تاریخ شروع</th>
                                    <th>تاریخ پایان</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($packageUsers->isNotEmpty())
                                    @foreach ($packageUsers as $index => $userPackege)
                                        <tr>
                                            <td>{{ $userPackege->id }}</td>
                                            <td>{{ $userPackege->package->name }}</td>
                                            <td>{{ $userPackege->type->getName() }}</td>
                                            <td>{{ verta($userPackege->start_at)->format('Y/m/d') }}</td>
                                            <td>{{ verta($userPackege->end_at)->format('Y/m/d') }}</td>
                                            <td>
                                                <div class="btn-group mt-2 mb-2">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                        عملیات <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li><a wire:click='removePackage({{$userPackege->id}})' wire:confirm='آیا از حذف این رژیم مطمعن هستید؟' href="#">حذف</a></li>
                                                        <li><a  href="{{route('admin.user.assign.edit' , ['user' => $user , 'packageUser' => $userPackege])}}">ویرایش</a></li>
                                                    </ul>
                                                </div>
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
