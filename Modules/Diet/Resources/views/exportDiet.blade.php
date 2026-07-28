<!doctype html>
<html lang="fa_IR" dir="rtl"
      style="--primary01:rgba(0, 112, 187, 0.1); --primary02:rgba(0, 112, 187, 0.2); --primary03:rgba(0, 112, 187, 0.3); --primary06:rgba(0, 112, 187, 0.6); --primary09:rgba(0, 112, 187, 0.9); --primary-bg-color:#0070bb; --primary-bg-hover:#0070bb95; --primary-bg-border:#0070bb; --dark-null:rgba(0, 112, 187, 0.5); --transparent-null:#0070bb; --primary-transparentcolor:#0070bb20; --darkprimary-null:#0070bb20; --transparentprimary-null:#0070bb20;">

<head>
    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1s.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="سیستم مدیریت">
    <meta name="author" content="شمیران وب">

    <link href="{{ admin_asset('css/style.css') }}" rel="stylesheet"/>
    <link href="{{ admin_asset('css/skin-modes.css') }}" rel="stylesheet"/>
</head>

<body class="rtl app sidebar-mini">
<div class="card mt-5" style="width: 1000px !important">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="text-end">
                    <span class="badge rounded-pill bg-info " style="align-self: end;"">کد رژیم:
                    {!! $dietRequest->id !!}</span>
                </div>
            </div>
            <div class="col-12 text-center">
                <img src="{{ admin_default_asset('logo.png') }}" alt="الان وقتشه"
                     style="height: 6rem;  line-height: 2rem;
                            vertical-align: middle;
                            width: auto;">
            </div>
            <div class="co-12 mt-3">
                <table class="table border" style="width: 950px">
                    <thead>
                    <tr>
                        <th>
                            <strong>نام</strong>:
                            <span>{{ $user->first_name ??  '-' }}</span>
                        </th>
                        <th>
                            <strong>نام خانوادگی</strong>:
                            <span>{{ $user->last_name ?? '-' }}</span>
                        </th>
                        <th>
                            <strong>وزن </strong> :
                            <span>{{ $dietRequest->user_information['WEIGHT'] }}</span>
                        </th>
                        <th>
                            <strong>وزن هدف</strong> :
                            <span>{{ $dietRequest->user_information['TARGET_WEIGHT'] }}</span>
                        </th>
                        <th>
                            <strong>سن</strong>:
                            <span>{{ $user->age() }}</span>
                        </th>
                    </tr>
                    </thead>

                </table>
            </div>
            <div class="col-12 mt-5">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">روز</th>
                        <th scope="col">نام وعده</th>
                        <th scope="col">نوع وعده</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($dietDetail as $day_number => $detail)
                        @foreach ($detail['foods'] as $eachmeal)
                            <tr>
                                <td scope="row">{{ $day_number + 1 }}</td>
                                <td>{{ $eachmeal['title'] }}</td>
                                <td>
                                    @if (isset($eachmeal['hand_written']))
                                        @if (!empty($eachmeal['hand_written']['title']))
                                            <Strong> {{ $eachmeal['hand_written']['title'] }} :</Strong>
                                        @endif
                                        {{ $eachmeal['hand_written']['body'] }}
                                    @endif
                                    @foreach ($eachmeal['meals'] as $eachmealTitle)
                                        @if (in_array($eachmealTitle['type'], ['protein', 'carb', 'fat', 'fiber']))
                                            <Strong>
                                                {{ Modules\Diet\Enum\MainNutritionEnum::tryFrom($eachmealTitle['type'])->getName() }}
                                                :</Strong>
                                        @endif
                                        {{ $eachmealTitle['title'] }}
                                        @if (!$loop->last)
                                            &nbsp;&nbsp;-&nbsp;&nbsp;
                                            @endif
                                            </strong>
                                            @endforeach
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script src="{{ admin_asset('plugins/jquery/jquery.min.js') }}"></script>
</body>

</html>
