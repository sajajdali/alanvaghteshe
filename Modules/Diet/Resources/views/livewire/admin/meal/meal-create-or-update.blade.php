<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">
                {{ $isEdited ? 'ویرایش وعده' : 'افزودن وعده جدید' }}
            </h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">
                        {{ $isEdited ? 'ویرایش وعده' : 'افزودن وعده جدید' }}
                    </h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent>
                        <div class="form-row">
                            <div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 mb-3">
                                <label for="name">نام وعده (الزامی)</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       wire:model="name" placeholder="نام">
                                @error('name')
                                <div id="validationName"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-9 mb-3">
                                <label for="iconUrl">آیکون وعده</label>
                                <input type="text" class="form-control @error('iconUrl') is-invalid @enderror"
                                       id="iconUrl"
                                       wire:model="iconUrl" placeholder="آدرس فایل آیکون ">
                                @error('iconUrl')
                                <div id="validationiconUrl"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <button
                                data-for="iconUrl"
                                data-variable="iconUrl"
                                class="btn btn-primary select_file col-3 mb-3 mt-5"
                                data-bs-target="#file-selector-modal"
                                data-bs-toggle="modal"
                                type="button">
                                انتخاب فایل
                            </button>
                        </div>
                        <div class="form-row">
                            <div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 mb-3">
                                <label for="priority">اولویت نمایش</label>
                                <input type="number" class="form-control @error('priority') is-invalid @enderror"
                                       id="priority"
                                       wire:model="priority" dir="ltr" placeholder="اولویت نمایش">
                                @error('priority')
                                <div id="priority"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" wire:loading.class="bg-gray btn-loading disabled"
                                wire:click="updateOrCreate">
                            @if($isEdited)
                                ویرایش وعده
                            @else
                                ایجاد وعده
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <livewire:admin::file-manager-modal/>

</div>

@push('scripts')
    <script>
        Livewire.on('select_file', (param) => {
        @this.set('iconUrl', param.url)
            ;
            //close modal
            $('#file-selector-modal').modal('hide');
        });
    </script>
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush
