@extends('admin::layouts.app')

@section('content')
    <div class="row row-sm mt-5">
        <div class="col-lg-12">
            <div class="card custom-card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">لیست کد تخفیف ها </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border text-nowrap text-md-nowrap table-striped">
                            <thead>
                            <tr class="text-center">
                                <th>#</th>
                                <th>عنوان</th>
                                <th>مبلغ</th>
                                <th>نوع مبلغ</th>
                                <th>کد</th>
                                <th>تعداد قابل استفاده</th>
                                <th>تعداد استفاده شده</th>
                                <th>شروع از</th>
                                <th>اتمام</th>
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($coupons as $coupon)
                                <tr class="text-center">
                                    <td>{{ $coupon->id }}</td>
                                    <td>{{ $coupon->title }}</td>
                                    <td>{{ number_format($coupon->value) }}</td>
                                    <td>{{ $coupon->is_percent == 0 ? 'ثابت' : 'درصدی' }}</td>
                                    <td>{{ $coupon->code }}</td>
                                    <td>{{ $coupon->usable_count }}</td>
                                    <td>{{ (int) $coupon->used }}</td>
                                    <td>{{ verta($coupon->start_at)->format('Y-m-d') }}</td>
                                    <td>{{ verta($coupon->end_at)->format('Y-m-d') }}</td>
                                    <td>{{ $coupon->active == 0 ? 'غیر فعال' : 'فعال' }}</td>
                                    <td>
                                        <div class="btn-group mt-2 mb-2">
                                            <button type="button" class="btn btn-primary dropdown-toggle"
                                                    data-bs-toggle="dropdown">
                                                عملیات <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li>
                                                    <a href="{{route('admin.coupon.edit',['coupon' => $coupon->id ])}}">ویرایش</a>
                                                </li>
                                                <!-- __BLOCK__ -->
                                                <li><a class="admin_sweet_alert op-0-4" data-title="خطا"
                                                       data-type="error"
                                                       data-description="امکان حذف این کاربر وجود ندارد."
                                                       href="#">حذف</a>
                                                </li>
                                                <!-- __ENDBLOCK__ -->
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{$coupons->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
