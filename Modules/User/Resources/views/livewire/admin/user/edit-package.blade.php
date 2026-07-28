<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">اختصاص بسته <span class="text-secondary">{{ $user->fullName }}</span></h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">اختصاص بسته</li>
                <li class="breadcrumb-item"><a href="{{ route('admin.user.index') }}">کاربران</a></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputEmail2">تاریخ شروع</label>
                                <input  type="text" class="form-control" wire:model="start_at" id="startDate_packages"
                                    placeholder="انتخاب کنید" autocomplete="off" data-jdp>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputEmail2">تاریخ پایان</label>
                                <input type="text" class="form-control" id="EndDate_package"
                                    placeholder="انتخاب کنید" wire:model="end_at"  autocomplete="off" data-jdp >
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-5">
                        <button wire:click='updatePackage' class="btn btn-success">
                            <span wire:loading.remove>ذخیره</span>
                            <div wire:loading class="spinner-border spinner-border-sm mt-1" role="status">
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
    <link rel="stylesheet"
          href="{{ admin_asset('css/jalalidatepicker.min.css') }}">
@endpush
@push('scripts')
    <script src="{{ admin_asset('js/jalalidatepicker.min.js') }}"></script>

    <script>
        document.addEventListener('livewire:init', function () {
            jalaliDatepicker.startWatch();
        });

        document.addEventListener('livewire:navigated', function () {
            jalaliDatepicker.startWatch();
        });
    </script>
@endpush
