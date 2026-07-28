<html>
<head>
    <link href="https://crm.jesmino.com//assets/admin/css/style.css?v=f47568726f7db3b49239cdb4d87bcd81" rel="stylesheet" />
    <link href="https://crm.jesmino.com//assets/admin/css/skin-modes.css?v=f47568726f7db3b49239cdb4d87bcd81" rel="stylesheet" />
    <style>
        /*make table full border*/
        table {
            border-collapse: collapse;
            width: 100%;
        }

        table td, table th {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        body {
        font-family: IRANSansX !important;
      }
    </style>
</head>
<body style="width: 21cm;margin: 0 auto;direction: rtl">
<div style="width: 100%; text-align: center">
    <h1>برنامه ورزشی</h1>
    <br>
    <table>
        <thead>
        @if($exercise_plan_request->user)
            <tr>
                <td colspan="6">
                    مشخصات کاربر
                </td>
            </tr>
            <tr>
                <td>
                    نام و نام خانوادگی: {{$exercise_plan_request->user->full_name}}
                </td>
                <td colspan="5">
                    شماره موبایل: {{ $exercise_plan_request->user->mobile }}
                </td>
            </tr>
        @endif
        <tr>
            <td colspan="6">
                برنامه
            </td>
        </tr>
        <tr>
            <td>
                تاریخ شروع: {{ jdate('l d F Y',$exercise_plan_request->start_at->timestamp) }}
            </td>
            <td colspan="3">
                تاریخ پایان: {{ jdate('l d F Y',$exercise_plan_request->end_at->timestamp) }}
            </td>
            <td colspan="2">
                تعداد جلسه در هفته: {{ $exercise_plan_request->session_count }}
            </td>
        </tr>
        </thead>
        <tbody>
        @for($i=0;$i<$exercise_plan_request->session_count;$i++)
            <tr>
                <td colspan="6">برنامه روز {{ $i+1 }}</td>
            </tr>
            @if($details->where('day',$i)->where('set_type',\Modules\Exercise\Enum\ExerciseSetTypeEnum::BEFORE_SET)->isNotEmpty())
                <tr>
                    <td colspan="6">قبل از تمرین</td>
                </tr>
                <tr>
                    <td>
                        {{ $details->where('day',$i)->where('set_type',\Modules\Exercise\Enum\ExerciseSetTypeEnum::BEFORE_SET)->first()->exercise_name }}
                    </td>
                    <td colspan="5">
                        {{ $details->where('day',$i)->where('set_type',\Modules\Exercise\Enum\ExerciseSetTypeEnum::BEFORE_SET)->first()->reps[0] ?? '' }}
                    </td>
                </tr>
            @endif
            <tr>
                <td>حرکت</td>
                <td style="width: 1cm">ست۱</td>
                <td style="width: 1cm">ست۲</td>
                <td style="width: 1cm">ست۳</td>
                <td style="width: 1cm">ست۴</td>
                <td style="width: 1cm">ست۵</td>
            </tr>
            @php($itemIndex=0)
            @foreach($details->where('day',$i)->where('set_type',\Modules\Exercise\Enum\ExerciseSetTypeEnum::MAIN_SET) as $mainSet)
                <tr style="background: @if($itemIndex%2==0) #f2f2f2 @else #ffffff @endif">
                    <td>
                        {{ $mainSet->exercise_name }} [@if($mainSet->has_super_set) سوپرست @else عادی @endif]
                    </td>
                    <td>{{ $mainSet->reps[0] ?? "*" }}</td>
                    <td>{{ $mainSet->reps[1] ?? "*" }}</td>
                    <td>{{ $mainSet->reps[2] ?? "*" }}</td>
                    <td>{{ $mainSet->reps[3] ?? "*" }}</td>
                    <td>{{ $mainSet->reps[4] ?? "*" }}</td>
                </tr>
                @if($mainSet->has_super_set)
                    @php($superSetMainSet = $mainSet->superSet)
                    <tr style="background: @if($itemIndex%2==0) #f2f2f2 @else #ffffff @endif">
                        <td>
                            {{ $superSetMainSet->exercise_name }}
                        </td>
                        <td>{{ $superSetMainSet->reps[0] ?? "*" }}</td>
                        <td>{{ $superSetMainSet->reps[1] ?? "*" }}</td>
                        <td>{{ $superSetMainSet->reps[2] ?? "*" }}</td>
                        <td>{{ $superSetMainSet->reps[3] ?? "*" }}</td>
                        <td>{{ $superSetMainSet->reps[4] ?? "*" }}</td>
                    </tr>
                @endif
                @php($itemIndex=$itemIndex+1)
            @endforeach

            @if($details->where('day',$i)->where('set_type',\Modules\Exercise\Enum\ExerciseSetTypeEnum::AFTER_SET)->isNotEmpty())
                <tr>
                    <td colspan="6">بعد از تمرین</td>
                </tr>
                <tr>
                    <td>
                        {{ $details->where('day',$i)->where('set_type',\Modules\Exercise\Enum\ExerciseSetTypeEnum::AFTER_SET)->first()->exercise_name }}
                    </td>
                    <td colspan="5">
                        {{ $details->where('day',$i)->where('set_type',\Modules\Exercise\Enum\ExerciseSetTypeEnum::AFTER_SET)->first()->reps[0] ?? '' }}
                    </td>
                </tr>
            @endif
        @endfor
        </tbody>
    </table>
</div>
</body>
</html>
