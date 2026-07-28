<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">
                {{ $isEdited ? 'ویرایش کاربر' : 'افزودن کاربر جدید' }}
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
                        {{ $isEdited ? 'ویرایش کاربر' : 'افزودن کاربر جدید' }}
                    </h3>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-row">
                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                <label for="userName">نام (الزامی)</label>
                                <input type="text" class="form-control @error('userName') is-invalid @enderror"
                                       id="userName"
                                       wire:model="userName" placeholder="نام">
                                @error('userName')
                                <div id="validationuserName"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                <label for="userLastName">نام خانوادگی (الزامی)</label>
                                <input type="text" class="form-control @error('userLastName') is-invalid @enderror"
                                       id="userLastName"
                                       wire:model="userLastName" placeholder="نام خانوادگی کاربر">
                                @error('userLastName')
                                <div id="validationuserLastName"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                <label for="userEmail">ایمیل (الزامی)</label>
                                <input type="text" class="form-control @error('userEmail') is-invalid @enderror"
                                       id="userEmail"
                                       wire:model="userEmail" placeholder="آدرس ایمیل">
                                @error('userEmail')
                                <div id="validationuseruserEmail"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                <label for="userMobile">موبایل</label>
                                <input type="text" class="form-control @error('userMobile') is-invalid @enderror"
                                       id="userMobile"
                                       wire:model="userMobile" placeholder="موبایل کاربر">
                                @error('userMobile')
                                <div id="validationuseruserMobile"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        @if($user !== null)
                            <div class="form-row">
                                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <label for="invitation_code">کد دعوت</label>
                                    <input type="text" class="form-control @error('invitation_code') is-invalid @enderror"
                                           id="invitation_code"
                                           wire:model="invitation_code" placeholder="نام">
                                    @error('invitation_code')
                                    <div id="invitation_code"
                                         class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <label for="referral_percentage">درصد به ازای معرفی</label>
                                    <input type="text" class="form-control @error('referral_percentage') is-invalid @enderror"
                                           id="referral_percentage"
                                           wire:model="referral_percentage" placeholder="درسد به ازای هر معرفی">
                                    @error('referral_percentage')
                                    <div id="referral_percentage"
                                         class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <label class="ckbox" for="ckbox-unchecked"><input wire:model="applyToAllPayments" type="checkbox" id="ckbox-unchecked"><span>دیافت درصد از همه پرداخت ها</span></label>
                                </div>
                            </div>
                        @endif
                        @if($user !== null)
                            <div class="form-row mb-2 mt-5">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        فقط در صروت نیاز به تغییر کلمه عبور فیلد‌های کلمه عبور و تکرار آن را پر کنید در
                                        غیر این صورت این
                                        دو فیلد را خالی بگزارید.
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="form-row">
                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                <label for="password">رمز عبور (الزامی)</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       wire:model="password" placeholder="رمز عبور">
                                @error('password')
                                <div id="validationuserpassword"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                <label for="password_confirmation">تکرار رمز (الزامی)</label>
                                <input type="password"
                                       class="form-control @error('password_confirmation') is-invalid @enderror"
                                       id="password_confirmation" placeholder="تکرار رمز عبور"
                                       wire:model="password_confirmation">
                                @error('password_confirmation')
                                <div id="validationuserpassword_confirmation"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-12">
                                <label for="password_confirmation">نقش‌</label>
                                @can('user')
                                    <br>
                                    نقش‌های کاربری
                                    @foreach($userRoles as $role)
                                        <div class="col-lg-12">
                                            <label class="ckbox" for="user_role_{{ $role->id }}">
                                                <input value="{{ $role->id }}" type="checkbox"
                                                       id="user_role_{{ $role->id }}"
                                                       wire:model="selectedRoles"><span>{{ $role->name }}
                                                    @if($role->hasPermissionTo('USER_DEFAULT'))
                                                        (نقش پیشفرض)
                                                    @endif
                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                    <br>
                                    نقش‌های مدیریتی
                                    @foreach($adminRoles as $role)
                                        <div class="col-lg-12">
                                            <label class="ckbox" for="user_role_{{ $role->id }}">
                                                <input value="{{ $role->id }}" type="checkbox"
                                                       id="user_role_{{ $role->id }}"
                                                       wire:model="selectedRoles"><span>{{ $role->name }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-warning">
                                        شما فقط در نقش پیشفرض کاربری می‌توانید این کاربر را ایجاد کنید.
                                    </div>
                                @endif
                                @error('selectedRoles')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @if(count($supportTeam) > 0)
                            <div class="form-row">
                                <div class="col-12 mb-3" wire:ignore>
                                    <label for="supporter">
                                        پشتیبانان کاربر
                                    </label>
                                    <select multiple class="form-control" data-placeholder="انتخاب پشتیبانان"
                                            id="supporter">
                                        @foreach($supportTeam as $userSupportId => $userSupport)
                                            <option value="{{ $userSupportId }}"
                                                    @if(in_array($userSupportId,$supporter,false)) SELECTED @endif>
                                                {{ $userSupport }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-secondary mt-3 mb-5">
                                برای ایجاد پشتیبان برای این کاربر، ابتدا باید یک کاربر پشتیبانی ایجاد کنید.
                            </div>
                        @endif
                        <div class="form-row">
                            <label for="password">تصویر کاربر</label>
                            <div class="input-group">
               <span class="input-group-btn">
                 <button
                     data-for="userAvatar"
                     data-variable="userAvatar"
                     class="btn btn-primary select_file"
                     data-bs-target="#file-selector-modal"
                     data-bs-toggle="modal"
                     type="button">
                   <i class="fa fa-picture-o"></i>
                     انتخاب تصویر
                 </button>
               </span>
                                <input id="thumbnail" class="form-control" type="text" name="filepath"
                                       wire:model="userAvatar">
                            </div>
                            <img id="holder" style="margin-top:15px;max-height:100px;" src="{{ $userAvatar }}"/>
                        </div>
                        <br>
                        <button type="button" class="btn btn-primary" wire:loading.class="bg-gray btn-loading disabled"
                                wire:click="updateOrCreate">
                            @if($user !== null)
                                ویرایش کاربر
                            @else
                                ایجاد کاربر
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
    <!-- SELECT2 JS -->
    <script src="{{admin_asset('plugins/select2/select2.full.min.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('#supporter').select2({
                dir: "rtl",
                placeholder: 'انتخاب پشتیبانان',
                allowClear: true,
                multiple: true,
                searchInputPlaceholder: 'جستجو',
                search: true,
                width: '100%',
            });

            $('#supporter').on('change', function (e) {
                var data = $('#supporter').select2("val");
            @this.set('supporter', data)
                ;
            });
        });
        Livewire.on('select_file', (param) => {
        @this.set('userAvatar', param.url)
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

