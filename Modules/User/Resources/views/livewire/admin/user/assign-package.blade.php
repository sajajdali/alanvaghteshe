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
                    @if ($userHasActivePackage)
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>توجه!</strong> این کاربر بسته ی
                            <strong>{{ $userHasActivePackage->package->name }}</strong> را تا تاریخ
                            {{ verta($userHasActivePackage->end_at)->format('Y/m/d') }} را دارد .
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    @endif
                    <div class="form-group" wire:ignore>
                        <label class="form-label mb-3">انتخاب بسته</label>
                        <select id="select_2" class="form-control select2-show-search form-select">
                            <option value="">انتخاب کنید</option>
                            @foreach (Modules\Package\Entities\Package::all() as $package)
                                <option value="{{ $package->id }}">
                                    {{ $package->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('selectedpackage')
                        <strong class="mb-2"> <span class="text-danger">{{ $message }}</span> </strong>
                    @enderror

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputEmail2">تاریخ شروع</label>
                                <input  type="email" class="form-control" id="startDate_package"
                                    placeholder="انتخاب کنید">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputEmail2">تاریخ پایان</label>
                                <input type="email" class="form-control" id="EndDate_package"
                                    placeholder="انتخاب کنید">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-5">
                        <button wire:click='assignpackage' class="btn btn-success">
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
@push('scripts')
    <script src="{{ admin_asset('plugins/select2/select2.full.min.js') }}"></script>

    <script>
        document.addEventListener('livewire:initialized', () => {
            $('#select_2').select2();
            $('#select_2').on('change', function() {
                var selectedValue = $(this).val();
                @this.set('selectedpackage', selectedValue);
            });

            $('#startDate_package').persianDatepicker({
                initialValue: false,
                autoClose: true,
                format: 'LLLL',
                onSelect: function(unix) {
                    @this.set('startDate_package', unix / 1000);
                },
            });
            $('#EndDate_package').persianDatepicker({
                initialValue: false,
                autoClose: true,
                format: 'LLLL',
                onSelect: function(unix) {
                    @this.set('EndDate_package', unix / 1000);
                },
            });
        })
    </script>
@endpush
