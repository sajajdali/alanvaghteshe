<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">تجویز رژیم برای <span class="text-secondary">{{ $user->fullName }}</span></h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">تجویز رژیم</li>
                <li class="breadcrumb-item"><a href="{{ route('admin.user.index') }}">کاربران</a></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @error('selectedDiet')
                    <div class="alert alert-secondary alert-dismissible fade show" role="alert">
                        <span class="alert-inner--text">{{ $message }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    @enderror
                    <div class="form-group" wire:ignore>
                        <label class="form-label mb-3">انتخاب رژیم</label>
                        <select wire:model='selectedDiet' id="select_2"
                                class="form-control select2-show-search form-select">
                            <option value="">انتخاب کنید</option>
                            @foreach (Modules\Diet\Entities\DietPlan::where('general_pattern', 0)->get() as $diet)
                                <option @if ($suggestedDiet == $diet->id) selected @endif value="{{ $diet->id }}">
                                    {{ $diet->name }}</option>
                            @endforeach
                        </select>

                        <p class="text-muted mt-2 ms-2">پلن پیشنهادی به صورت پیشفرض انتخاب شده است</p>
                    </div>
                    <div class="form-group" wire:ignore>
                        <label class="form-label mb-3">ارسال پیامک</label>
                        <label class="ckbox" for="user_role_2">
                            <input value="2" type="checkbox" id="user_role_2" wire:model="sendSmsAfterSendDiet"><span>ارسال پیامک پس از تجویز رژیم </span>
                        </label>
                    </div>

                    <div class="d-flex justify-content-end mt-5">
                        <button wire:click='assignDiet' class="btn btn-success">
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
            $('#select_2').on('change', function () {
                var selectedValue = $(this).val();
            @this.set('selectedDiet', selectedValue)
                ;
            });
        })
    </script>
@endpush
