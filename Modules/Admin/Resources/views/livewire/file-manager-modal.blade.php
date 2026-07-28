<div>
    <!-- modal -->
    <div class="modal fade @if($isDialogShow) show @endif" data-bs-backdrop="static" id="file-selector-modal"
         @if($isDialogShow) aria-modal="true" role="dialog" style="display: block;" @endif>
        <div class="modal-dialog modal-xl modal-dialog-top" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">انتخاب فایل</h6>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"
                            id="btn_close_upload"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2" wire:ignore>
                        <input type="file" class="dropify" id="dropify" wire:model="uploadFile" data-height="200"/>
                    </div>
                    <button class="btn ripple btn-secondary"
                            id="btn_cancel_upload"
                            wire:target="upload"
                            wire:loading.class="op-0-3"
                            wire:loading.attr="disabled"
                            data-bs-dismiss="modal" type="button">انصراف
                    </button>
                </div>
                <div class="modal-footer justify-content-start">
                    <div class="breadcrumb-main">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                @foreach($broadcamp as $indexItem => $item)
                                    @if($indexItem === 0)
                                        <li class="breadcrumb-item active">
                                            <button
                                                class="text-primary"
                                                wire:click="goTo({{ $indexItem }})">{{ $item['title'] }}</button>
                                        </li>
                                    @else
                                        <li class="breadcrumb-item">
                                            <button
                                                wire:click="goTo({{ $indexItem }})">{{ $item['title'] }}</button>
                                        </li>
                                    @endif
                                @endforeach
                            </ol>
                        </nav>
                    </div>
                    @if(count($files['files']) > 0 || count($files['folders']) > 0)
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap text-md-nowrap table-bordered" wire:loading.class="op-0-3">
                                <thead>
                                <tr>
                                    <th scope="col" style="width: 32px">#</th>
                                    <th scope="col">نام</th>
                                    <th scope="col" style="width: 64px">انتخاب</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($files['folders']) > 0)
                                    @foreach($files['folders'] as $folder)
                                        <tr style="cursor: pointer" wire:click="goToDirectory('{{ $folder }}')">
                                            <td>{{ $loop->iteration }}</td>
                                            <td colspan="2"><img src="{{admin_asset('images/files/folder.png')}}"
                                                                 alt="img"
                                                                 style="width: 36px">
                                                {{ $folder }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                @if(count($files['files']) > 0)
                                    @foreach($files['files'] as $fileItem)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ $fileItem['url'] }}" target="_blank">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <img
                                                                src="{{ $fileItem['icon'] }}"
                                                                alt="img"
                                                                style="width: 36px">
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ $fileItem['name'] }}
                                                            </h6> <span
                                                                class="text-muted fs-13">{{ $fileItem['size'] }}</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </td>
                                            <td>
                                                <button
                                                    type="button"
                                                    class="btn btn-success"
                                                    wire:target="selectFile('{{ $fileItem['url'] }}')"
                                                    wire:loading.class.remove="btn-success"
                                                    wire:loading.class="btn-loading disabled btn-light"
                                                    wire:loading.attr="disabled"
                                                    wire:click="selectFile('{{ $fileItem['url'] }}')"><i
                                                        class="fe fe-check me-2"></i>انتخاب
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info col-12">
                            هیچ فایل یا پوشه ای وجود ندارد
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- End modal -->
</div>
@push('scripts')
    <!--Internal Fileuploads js-->
    <script src="{{admin_asset('plugins/fileuploads/js/fileupload.js')}}"></script>
    <script src="{{admin_asset('plugins/sweet-alert/sweetalert.min.js')}}"></script>
    <style>
        .breadcrumb-item + .breadcrumb-item:before {
            transform: rotate(180deg);
        }
    </style>
    <script>
        var dropifyInput;
        $(document).ready(function () {
            dropifyInput = $('#dropify').dropify({
                allowedFileExtensions: ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'txt', 'xlsx', 'xls', 'zip', 'mp4'],
                messages: {
                    'default': 'فایل را اینجا بکشید یا کلیک کنید',
                    'replace': 'فایل را اینجا بکشید یا کلیک کنید',
                    'remove': 'حذف',
                    'error': 'خطا در بارگزاری فایل'
                },
                error: {
                    'allowedExtension': 'فایل انتخابی مجاز نمی باشد',
                }
            });
        });
        //on model show
        $('#file-selector-modal').on('show.bs.modal', function (e) {
            Livewire.dispatch('showDialog');
        });
        //on hide modal
        $('#file-selector-modal').on('hide.bs.modal', function (e) {
            Livewire.dispatch('hideDialog');
        });
        Livewire.on('error_file_manager_modal', (param) => {
            swal({
                title: "خطا",
                text: "" + param.message,
                type: "error",
                showCancelButton: true,
                allowOutsideClick: true,
                showConfirmButton: false,
                cancelButtonText: "متوجه شدم",
                closeOnConfirm: false
            });
        });
        Livewire.on('upload_complete', () => {
            swal({
                title: "موفق",
                text: "فایل با موفقیت بارگذاری شد",
                type: "success",
                showCancelButton: false,
                allowOutsideClick: true,
                showConfirmButton: false,
                timer: 2000,
                closeOnConfirm: false
            });
            //clear dropify
            dropifyInput.data('dropify').resetPreview();
        });
    </script>
@endpush
