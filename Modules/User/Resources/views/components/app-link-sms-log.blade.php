<div>
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="card-title">
                        لینک اپلیکیشن پیامک شده
                    </div>
                    <div class="table-responsive">
                        <table class="table border text-nowrap text-md-nowrap table-striped text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>ارسال شده توسط</th>
                                <th>در زمان</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($user->smsLog()->isNotEmpty())
                                @foreach ($user->smsLog() as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>{{ $log->loggedBy->fullName }}</td>
                                        <td>{{ verta($log->created_at)->format('Y/m/d ساعت H:i') }}</td>
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
