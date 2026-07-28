<div>

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card">
                <div class="card-header border-bottom d-flex justify-content-between">
                    <h3 class="card-title">پرداخت های کاربر</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border text-nowrap text-md-nowrap table-striped text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>نام بسته</th>
                                <th>وضعیت</th>
                                <th>مبلغ</th>
                                <th>تخفیف</th>
                                <th>مبلغ کل</th>
                                <th>تاریخ </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($transactions->isNotEmpty())
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->id }}</td>
                                        <td>@if(isset($transaction->detail['package_id'])) {{ $this->getPackageName($transaction->detail['package_id']) }}  @else - @endif</td>
                                        <td>{{$transaction->status->getName()}}</td>
                                        <td>{{ number_format($transaction->cost) }}</td>
                                        <td>{{ number_format($transaction->discount) }}</td>
                                        <td>{{ number_format($transaction->total_cost) }}</td>
                                        <td>{{ verta($transaction->created_at)->format('Y/m/d ساعت H:i:s') }}</td>

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
                        @if($user->invitedBy()->count())
                            <div class="alert alert-dark alert-dismissible fade show" role="alert">
                                <span class="alert-inner--text">این کاربر توسط
                                {{$user->invitedBy->user->full_name}}
                                    دعوت شده است
                                    و کد دعوت وی را وارد کرده است
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
