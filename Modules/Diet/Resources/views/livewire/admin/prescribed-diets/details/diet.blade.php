<div>
    <h3>جزئیات رژیم تجویز شده</h3>
    <hr>
    <table class="table border text-nowrap text-md-nowrap table-striped px-5">
        <tbody>
        @if(isset($dietRequest))
                <tr>
                    <th class="border-start">
                        <strong>پلن انتخابی</strong>
                    </th>
                    <td>{!! isset($dietRequest->diet_plan_id) ? '<a href="'.route('admin.diet_plan.edit' , $dietRequest->dietPlan).'">'.$dietRequest->dietPlan->name.'</a>' : ' یافت نشد '  !!}
                        <button type="button"
                                class="btn btn-secondary btn-sm"
                                wire:click="toggleChangeDiet"
                                wire:loading.class="btn-loading"
                                wire:loading.attr="disabled">
                            تغییر پلن انتخابی
                        </button>
                        @if($changeDiet)
                            <div class="alert alert-info mt-3">
                                <div class="form-group">
                                    <label class="form-label" for="default-dropdown">پلن مورد نظر شما</label>
                                    <select wire:model="form.selectedPlan"  class="form-control form-select" id="default-dropdown" data-bs-placeholder="Select Country">
                                        <option value="">انتخاب کنید</option>
                                        @foreach($dietPlans as $dietPlan)
                                            <option value="{{$dietPlan->id}}">{{$dietPlan->name}}</option>
                                        @endforeach

                                    </select>
                                    @error('form.selectedPlan') <div style="display: block" class="invalid-feedback">   {{ $message }}  </div> @enderror
                                </div>
                                <div class="form-group">
                                    <button type="button"
                                            class="btn btn-success "
                                            wire:click="changePlan"
                                            wire:loading.class="btn-loading"
                                            wire:loading.attr="disabled">
                                        تغییر پلن انتخابی
                                    </button>
                                </div>
                            </div>
                        @endif
                        @if(isset($successFully))
                            <div class="alert alert-success mt-4 alert-dismissible fade show" role="alert">
                                <span class="alert-inner--text">{{$successFully}}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                        @endif
                    </td>
                </tr>
            @if(isset($dietRequest->detail['report_diet']) && isset($dietRequest->detail['report_diet']['data']['calorieBreakdown']['meals']))
                <tr>
                    <th class="border-start">
                        <strong>کالری کل</strong>
                    </th>
                    <td>{{$dietRequest->detail['report_diet']['data']['calorieBreakdown']['total']}} کالری </td>
                </tr>
                @foreach($dietRequest->detail['report_diet']['data']['calorieBreakdown']['meals'] as $calorieBreakdown)
                    <tr>
                        <th class="border-start">
                            <strong>وعده {{$calorieBreakdown['meal_name']}}</strong>
                        </th>
                        <td>{{$calorieBreakdown['calories']}} کالری </td>
                    </tr>
                @endforeach

            @endif
        @endif
        </tbody>
    </table>
</div>
