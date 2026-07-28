<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">
                {{ $isEdited ? 'ویرایش نکته تغذیه' : 'افزودن نکته جدید' }}
            </h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">
                        {{ $isEdited ? 'ویرایش نکته' : 'افزودن نکته جدید' }}
                    </h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent>
                        <div class="form-row">
                            <div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 mb-3">
                                <label for="name">نام </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       wire:model="name" placeholder="نام">
                                @error('name')
                                <div id="validationName"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 mb-3">
                                <label for="name">نکته تغذیه </label>
                                <textarea wire:model='description' class="form-control" id="validationTextarea"
                                          placeholder="متن توضیحات اضافی"></textarea>
                                @error('description')
                                <div id="reduced_calories"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary" wire:loading.class="bg-gray btn-loading disabled"
                                wire:click="updateOrCreate">
                            @if($isEdited)
                                ویرایش
                            @else
                                ایجاد
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

