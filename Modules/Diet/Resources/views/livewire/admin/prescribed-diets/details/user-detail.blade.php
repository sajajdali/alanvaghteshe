<div>
    <table class="table border text-nowrap text-md-nowrap table-striped px-5">
        <tbody>
        <tr>
            <th class="border-start"><strong>شماره کاربری</strong></th>
            <td><a href="{{route('admin.user.document' , $assignedUser)}}">{{ $assignedUser->id }}</a></td>
        </tr>
        <tr>
            <th class="border-start"><strong>شماره موبایل</strong></th>
            <td><a href="{{route('admin.user.document' , $assignedUser)}}">{{$assignedUser->mobile }}</a></td>
        </tr>
        <tr>
            <th class="border-start"><strong>نام</strong></th>
            <td>{{ $userInfo['FIRST_NAME'] }}</td>
        </tr>
        <tr>
            <th><strong>نام خانوادگی</strong></th>
            <td>{{ $userInfo['LAST_NAME'] }}</td>
        </tr>
        <tr>
            <th><strong>تاریخ تولد</strong></th>
            @php
                $birthday = json_decode($userInfo['BIRTHDAY']);
            @endphp
            <td>{{ $birthday->year . '/' . $birthday->month . '/' . $birthday->day }}</td>
        </tr>
        <tr>
            <th><strong>جنسیت</strong></th>
            <td>{{ Modules\User\Enum\UserMetaEnum::tryFrom('5')->getOptionName($userInfo['GENDER']) }}</td>
        </tr>

        <tr>
            <th><strong>هدف انتخابی در زمان عضویت</strong></th>
            <td>{{ Modules\User\Enum\UserMetaEnum::tryFrom('6')->getOptionName($userInfo['DIET_TYPE']) }}</td>
        </tr>
        <tr>
            <th><strong>تاریخ تولد</strong></th>
            @php
                $birthday = json_decode($userInfo['BIRTHDAY']);
            @endphp
            <td>{{ $birthday->year . '/' . $birthday->month . '/' . $birthday->day }}</td>
        </tr>
        <tr>
            <th><strong>قد</strong></th>
            <td>{{ $userInfo['TALL'] }}</td>
        </tr>
        <tr>
            <th><strong>وزن</strong></th>
            <td>{{ $userInfo['WEIGHT'] }}</td>
        </tr>
        <tr>
            <th><strong>وزن هدف</strong></th>
            <td>{{ $userInfo['TARGET_WEIGHT'] }}</td>
        </tr>




        <tr>
            <th><strong>بیماری ها </strong></th>
            @if (! empty($disease))
                <td>
                    @foreach ($disease as $name)
                        {{$name}} @if(!$loop->last)
                            ,
                        @endif
                    @endforeach
                </td>
            @else
                <td> --</td>
            @endif
        </tr>
        <tr>
            <th><strong>محدودیت غذایی</strong></th>
            @if (count(json_decode($userInfo['FOOD_RESTRICTION'], true)) > 1)
                <td>
                    @foreach (json_decode($userInfo['FOOD_RESTRICTION'], true) as $FOOD_RESTRICTION)
                        {{ Modules\User\Enum\UserMetaEnum::tryFrom('18')->getOptionName($FOOD_RESTRICTION) }} @if (!$loop->last)
                            -
                        @endif
                    @endforeach
                </td>
            @else
                <td> --</td>
            @endif
        </tr>


        <tr>
            <th><strong>چند وقته ورزش میکنی</strong></th>
            <td>{{ Modules\User\Enum\UserMetaEnum::tryFrom('21')->getOptionName($userInfo['HOW_MUCH_EXPERIENCE_SPORTS']) }}
            </td>
        </tr>
        <tr>
            <th><strong>هدف از ورزش کردن</strong></th>
            <td>{{ Modules\User\Enum\UserMetaEnum::tryFrom('22')->getOptionName($userInfo['TARGET_OF_EXERCISE']) }}
            </td>
        </tr>
        <tr>
            <th><strong>میزان فعالیت</strong></th>
            <td>{{ Modules\User\Enum\UserMetaEnum::tryFrom('23')->getOptionName($userInfo['ACTIVITY_PER_WEEK']) }}
            </td>
        </tr>
        <tr>
            <th><strong>چند روز در هفته ورزش میکنی</strong></th>
            <td>{{ Modules\User\Enum\UserMetaEnum::tryFrom('24')->getOptionName($userInfo['HOW_MANY_DAYS_WEEK_EXERCISE']) }}
            </td>
        </tr>
        <tr>
            <th><strong>هدف</strong></th>
            <td>{{ Modules\User\Enum\UserMetaEnum::tryFrom('26')->getOptionName($userInfo['DIET_PLAN']) }}</td>
        </tr>
        <tr>
            <th><strong>نقاط ضعف</strong></th>
            <td>
                @if(isset($userInfo['WEAKNESSES_BODY']))
                    @foreach ( json_decode($userInfo['WEAKNESSES_BODY'], true) as $weaknesses)
                        {{ Modules\User\Enum\UserMetaEnum::tryFrom('27')->getOptionName("$weaknesses") }} @if (!$loop->last)
                            -
                        @endif
                    @endforeach
                @else
                    -
                @endif
            </td>
        </tr>
        </tbody>
    </table>
</div>
