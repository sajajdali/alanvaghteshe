<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">مدیریت تنظیمات</h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row">
        <div class="col-lg-4 col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">بخش‌های تنظیمات</h3>
                </div>
                <div class="card-body">
                    <ul class="nav1 nav-column flex-column br-7">
                        @foreach($menuSections as $menu)
                            <li class="nav-item1">
                                <a class="nav-link thumb text-dark-light @if($menu['id'] === $section) active @endif"
                                   wire:click="changeMenu('{{ $menu['id'] }}')"
                                   href="javascript:void(0)"><i
                                        class="{{ $menu['icon'] }} me-2"></i> {{ $menu['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-12">
            <div class="card custom-card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">بخش تنظیمات</h3>

                    <div class="card-options">
                        <button class="btn btn-secondary"
                                wire:loading.class="bg-gray btn-loading disabled"
                                wire:click="storeSetting">
                            ذخیره تغییرات
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        @if($options != null && count($options))
                            @foreach($options as $settingItem)
                                @livewire($settingItem->render(),
                                ['meta' => $settingItem , 'old_value' => $settingValues[$settingItem->value] ?? null]
                                ,key($settingItem->value))
                            @endforeach
                        @endif
                    </p>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-secondary"
                            wire:loading.class="bg-gray btn-loading disabled"
                            wire:click="storeSetting">
                        ذخیره تغییرات
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')

    <!-- WYSIWYG Editor JS -->
    <script src="//cdn.ckeditor.com/4.20.2/full/ckeditor.js"></script>
    <script src="{{ admin_asset('js/stand-alone-button.js') }}"></script>
    <script src="{{admin_asset('plugins/select2/select2.full.min.js')}}"></script>
    <script src="{{admin_asset('plugins/notify/js/notifIt.js')}}"></script>

    <script>
        function showSuccessMessage($message) {
            notif({
                msg: $message,
                type: "primary",
                position: "bottom",
                bottom: '10',
                time: 500
            });
        }

        Livewire.on('success-saving', param => {
            showSuccessMessage(param.message);
        });
        $(document).ready(function (){
            $('.image_handler').each(function(e){
            });

            $('.disable_button_150').on('change',function(){

            });
        });

    </script>
@endpush
