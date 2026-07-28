<div>
    <h3>شرایط بدنی</h3>
    <hr>
    <table class="table border text-nowrap text-md-nowrap table-striped px-5">
        <tbody>
        @isset($conditions['condition'])
            @foreach(app(\Modules\Diet\Service\DietService::class)->gtePersianConditionName($conditions['condition']) as $condition)
                <tr>
                    <th class="border-start">
                        <strong>{{ $condition['name'] }}</strong>
                    </th>
                    <th class="border-start">
                        @foreach($condition['value'] as $conditionValues)
                            {{ $conditionValues}}
                            @if(!$loop->last)  <br> @endif
                        @endforeach

                    </th>

                </tr>
            @endforeach
{{--            @foreach ($conditions['condition'] as $key => $condition)--}}
{{--                @continue(in_array($key , [5 , 6]))--}}
{{--                <tr>--}}
{{--                    <th class="border-start">--}}
{{--                        <strong>{{ Modules\Diet\Enum\ConditionKeyEnum::tryFrom(\Modules\Diet\Entities\Condition::find($key)->key->value)->getName() }}</strong>--}}
{{--                    </th>--}}
{{--                    @if (!empty($condition) && $key != 4)--}}
{{--                        <td>--}}
{{--                            @foreach ($condition as $keyToCheck)--}}
{{--                                {{ Modules\Diet\Entities\Condition::coditionValue(\Modules\Diet\Entities\Condition::find($key)->key->value, $keyToCheck) }}--}}
{{--                                @if(!$loop->last)--}}
{{--                                    ---}}
{{--                                @endif--}}
{{--                            @endforeach--}}
{{--                        </td>--}}
{{--                    @elseif ($key == 4 && isset($this->disease))--}}
{{--                        <td>--}}
{{--                            @foreach ($this->disease as $diseases)--}}
{{--                                {{ $diseases }}@if(! $loop->last)--}}
{{--                                    ,--}}
{{--                                @endif--}}
{{--                            @endforeach--}}
{{--                        </td>--}}
{{--                    @endif--}}
{{--                </tr>--}}
{{--            @endforeach--}}
        @endisset
        </tbody>
    </table>
    <div class="form-group">
        @if(!$changeConditions)
            <button type="button"
                    class="btn btn-warning "
                    wire:click="editConditions"
                    wire:loading.class="btn-loading"
                    wire:loading.attr="disabled">
                ویرایش شرایط
            </button>
        @endif
        @if($changeConditions)
            <form wire:submit.prevent class="mt-5">
                @foreach($fetchData['conditions'] as $condition)
                    @continue(in_array($condition->id , [5 , 6]))
                    <div class="col-12 ">
                        <div class="">
                            <div class="form-group">
                                <label class="form-label">{{$condition->key->getName()}}</label>
                                <div class="selectgroup selectgroup-pills">
                                    @if($condition->key->value == \Modules\Diet\Enum\ConditionKeyEnum::DISEASE->value)
                                        @foreach($fetchData['disease'] as $mealKey => $metaValue)
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="form.foodRestriction[]"
                                                       value="{{$mealKey}}"
                                                       class="selectgroup-input"
                                                       wire:model="form.conditions.{{$condition->id}}.{{$metaValue->id}}">
                                                <span class="selectgroup-button">{{$metaValue->name}}</span>
                                            </label>
                                        @endforeach
                                    @else
                                        @foreach($condition->options['items']['values'] as $optionKey => $optionValue)
                                            <label class="selectgroup-item">
                                                <input type="checkbox" name="form.foodRestriction[]"
                                                       value="{{$optionKey}}"
                                                       class="selectgroup-input"
                                                       wire:model="form.conditions.{{$condition->id}}.{{$optionKey}}">
                                                <span class="selectgroup-button">{{$optionValue}}</span>
                                            </label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        @error('form.meals')
                        <div id="validationName"
                             class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    @unless($loop->last)
                        <hr>
                    @endunless
                @endforeach


                <button type="submit" class="btn btn-primary" wire:loading.class="bg-gray btn-loading disabled"
                        wire:click="saveConditions">
                    ثبت تغییر
                </button>

                <button type="button"
                        class="btn btn-warning "
                        wire:click="editConditions"
                        wire:loading.class="btn-loading"
                        wire:loading.attr="disabled">
                    انصراف
                </button>
            </form>
        @endif
        @if(isset($successFully))
            <div class="alert alert-success mt-4 alert-dismissible fade show" role="alert">
                <span class="alert-inner--text">{{$successFully}}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        @endif
    </div>

</div>
