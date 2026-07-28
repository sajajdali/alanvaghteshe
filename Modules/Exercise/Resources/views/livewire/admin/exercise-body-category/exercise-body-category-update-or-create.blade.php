<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">
                {{ $isEdited ? 'ویرایش دسته' : 'افزودن دسته جدید' }}
            </h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">
                        {{ $isEdited ? 'ویرایش دسته' : 'افزودن دسته جدید' }}
                    </h3>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-row">
                            <div class="col-12 mb-3">
                                <label for="name">نام دسته (الزامی)</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                       wire:model="name" placeholder="نام">
                                @error('name')
                                <div id="validationuserName" class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 mb-3" wire:ignore>
                                <label for="parent_id">دسته والد</label>
                                <select  class="form-control" data-placeholder="بدون والد (دسته اصلی)" id="parent_id" >
                                    <option value="">بدون والد (دسته اصلی)</option>
                                    @foreach($parentCategories as $id => $value)
                                        <option value="{{ $id }}" @if($parent_id==$id) SELECTED @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <br>
                        <button class="btn btn-primary" wire:loading.class="bg-gray btn-loading disabled"
                                wire:click.prevent="updateOrCreate">
                            @if($isEdited)
                                ویرایش دسته
                            @else
                                ایجاد دسته
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <!-- SELECT2 JS -->
    <script src="{{admin_asset('plugins/select2/select2.full.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#parent_id').select2({
                dir: "rtl",
                placeholder: 'بدون والد (دسته اصلی)',
                allowClear: true,
                width: '100%',
            });
            $('#parent_id').on('change', function (e) {
                var data = $('#parent_id').select2("val");
                @this.set('parent_id', data);
            });
        });
    </script>
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush
