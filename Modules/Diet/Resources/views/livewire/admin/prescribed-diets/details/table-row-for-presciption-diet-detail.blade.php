<tr @if ($detail_parent->replaced_parent_id ===null) style="background-color:#cefdd2 ;" @else style="opacity : 0.3 ;" @endif>

    <td class="text-center">
        {{ $detail_parent->id }}
    </td>
    <td class="text-center">
        {{ $detail_parent->day_number }}
    </td>
    <td>
        {{ verta($detail_parent->date_of_day)->format('Y/m/d') }}
    </td>
    <td>

        <a href="{{route('admin.food.edit' ,$detail_parent->foodable->id )}}"> {{app(\Modules\Diet\Service\DietService::class)->handleMealName($dietReqeust , $detail->meal)}}</a> - <span class="rounded-pill bg-danger text-white p-1">جایگزین شده</span>
    </td>
    <td>
        @if ($foodTypeIsComined)
            @foreach ($this->complexFoodResult($detail_parent) as $items)
                {{ $items }} </br>
            @endforeach
        @else
            {{ $detail_parent->number_of_unit }}
            {{ $detail_parent->foodClass->foodUnit?->name }}
            {{ $detail_parent->foodClass->name }}
        @endif
    </td>

    <td>
        {{ $detail_parent->protein }}
    </td>
    <td>
        {{ $detail_parent->carb }}
    </td>
    <td>
        {{ $detail_parent->fat }}
    </td>
    <td>
        {{ $detail_parent->fiber }}
    </td>
    <td>
        {{ $detail_parent->calories }}
    </td>
    <td class="text-center">

        {!! $detail_parent->is_done != 0
            ? '<i class="fa fa-check text-success fs-5" data-bs-toggle="tooltip" title="" data-bs-original-title="صرف شده" aria-label="fa fa-check"></i>'
            : '<i class="fa fa-minus text-muted fs-5" data-bs-toggle="tooltip" title="" data-bs-original-title="صرف نشده" aria-label="fa fa-minus"></i>' !!}
    </td>
</tr>
@if ($detail_parent->replaced_parent_id == !null)
    @include('diet::livewire.admin.prescribed-diets.details.table-row-for-presciption-diet-detail' , ['detail_parent' => $this->parentDietdetail($detail_parent)])
@endif
