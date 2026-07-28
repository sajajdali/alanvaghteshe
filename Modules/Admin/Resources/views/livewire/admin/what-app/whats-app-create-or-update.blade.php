<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">
                {{ $isEdited ? 'ویرایش دستگاه' : 'افزودن دستگاه جدید' }}
            </h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    @if(\Modules\Admin\app\Models\WhatsappSession::count())
        <div class="col-md-12 alert alert-danger fade show" role="alert">
            <i class="fa fa-remove me-2" aria-hidden="true"></i>
           شما حداکثر میتوانید یک دستگاه اضافه کنید
        </div>
    @else
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">
                        {{ $isEdited ? 'ویرایش دستگاه' : 'افزودن دستگاه جدید' }}
                    </h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent>
                        <div class="form-row">
                            <div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 mb-3">
                                <label for="name">نام دستگاه (الزامی)</label>
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
                                <label for="name">آدرس webhook  (اختیاری)</label>
                                <input type="text" class="form-control @error('webhook_url') is-invalid @enderror"
                                       id="webhook_url"
                                       wire:model="webhook_url" placeholder="نام">
                                @error('webhook_url')
                                <div id="validationName"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" wire:loading.class="bg-gray btn-loading disabled"
                                wire:click="updateOrCreate">
                            @if($isEdited)
                                ویرایش دستگاه
                            @else
                                ایجاد دستگاه جدید
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
