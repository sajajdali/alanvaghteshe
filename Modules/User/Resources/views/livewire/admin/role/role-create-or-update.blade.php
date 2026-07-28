<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">ایجاد نقش</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.role.index') }}">مدیریت نقش‌ها</a></li>
                <li class="breadcrumb-item active" aria-current="page">ایجاد نقش جدید</li>
            </ol>
        </div>
    </div>

    <!-- row -->
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">فرم ایجاد نقش</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">برای ایجاد نقش جدید اطلاعات زیر را تکمیل نمایید.</p>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="p-2">
                                <div class="form-group">
                                    <input class="form-control" placeholder="نام نقش را وارد کنید" type="text"
                                           wire:model="name">
                                    @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-12">
                                        <label class="rdiobox" for="rdio_role_is_user">
                                            <input name="role_type" value="user" type="radio" id="rdio_role_is_user"
                                                   class="radio-primary" wire:model.live="roleType">
                                            <span>نقش کاربری</span>
                                        </label>
                                    </div>
                                    <div class="col-lg-12">
                                        <label class="rdiobox" for="rdio_role_is_admin">
                                            <input name="role_type" value="admin" type="radio" id="rdio_role_is_admin"
                                                   class="radio-primary" wire:model.live="roleType">
                                            <span>نقش مدیریتی</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    @if($roleType == 'user')
                                        <div class="material-switch">
                                            <input id="isUserDefault" name="default_role" wire:model="isRoleDefault"
                                                   value="1"
                                                   type="checkbox"/>
                                            <label for="isUserDefault" class="label-info"></label>
                                            فعال‌سازی این نقش به عنوان نقش پیشفرض
                                        </div>
                                    @else
                                        @foreach($permissions as $parentIndex => $permissionGroup)
                                            @foreach($permissionGroup as $index => $permission)
                                                <div class="row mt-4">
                                                    <div class="col-lg-12">
                                                        <div
                                                            class="expanel expanel-{{ $permission['type'] ?? 'info' }}">
                                                            <div class="expanel-heading">
                                                                <h3 class="expanel-title">{{ $permission['display_name'] ?? '' }}
                                                                    <div class="pull-left">
                                                                        <label class="rdiobox"
                                                                               for="rdio-disabled-{{ $parentIndex }}-{{ $index }}">
                                                                            <input class="panel_change_state"
                                                                                   type="radio"
                                                                                   value="0"
                                                                                   @if(!$isEdit || (count(array_intersect($selectedPermissions,array_keys($permission['gate'])))==0)) CHECKED
                                                                                   @endif
                                                                                   data-action="disable"
                                                                                   data-panel-id="{{ $index }}"
                                                                                   data-panel-parent-id="{{ $parentIndex }}"
                                                                                   id="rdio-disabled-{{ $parentIndex }}-{{ $index }}"
                                                                                   name="panel_state_{{ $parentIndex }}_{{ $index }}">
                                                                            <span>عدم دسترسی</span>
                                                                        </label>
                                                                    </div>
                                                                    @foreach($permission['gate'] as $gateKey => $gateValue)
                                                                        <div class="pull-left">
                                                                            <label class="rdiobox"
                                                                                   for="rdio-{{ $gateKey }}">
                                                                                <input class="panel_change_state"
                                                                                       type="radio"
                                                                                       data-panel-id="{{ $index }}"
                                                                                       data-panel-parent-id="{{ $parentIndex }}"
                                                                                       value="{{ $gateKey }}"
                                                                                       @if($isEdit && in_array($gateKey,$selectedPermissions,false)) CHECKED
                                                                                       @endif
                                                                                       data-action="enable"
                                                                                       id="rdio-{{ $gateKey }}"
                                                                                       name="panel_state_{{ $parentIndex }}_{{ $index }}">
                                                                                <span>{{ $gateValue }}</span>
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                </h3>
                                                            </div>
                                                            <div class="expanel-body"
                                                                 id="panel_permission_{{ $parentIndex }}_{{ $index }}"
                                                                 @if(count(array_intersect(array_keys($permission['gate']),$selectedPermissions)) === 0)
                                                                     style="display: none"
                                                                @endif>
                                                                @foreach($permission['permissions'] as $permissionKey => $permissionValue)
                                                                    <div class="col-lg-12">
                                                                        <label class="ckbox">
                                                                            <input type="checkbox"
                                                                                   value="{{ $permissionKey }}"
                                                                                   wire:model="selectedPermissions">
                                                                            <span>{{ $permissionValue }}</span>
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    @endif
                                </div>
                                <button class="btn btn-primary" wire:loading.class="bg-gray btn-loading disabled"
                                        wire:click="updateOrCreate">
                                    @if($isEdit)
                                        ویرایش نقش
                                    @else
                                        ایجاد نقش
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(document).ready(function () {
            $('body').on('change', '.panel_change_state', function () {
                let $action = $(this).data('action');
                let $panelId = $(this).data('panel-id');
                let $value = $(this).val();
                let $panelParentId = $(this).data('panel-parent-id');
                $('input[name="panel_state_' + $panelParentId + '_' + $panelId + '"]').each(function () {
                @this.removeItem($(this).val());
                });
                if ($action == 'disable') {
                    $('#panel_permission_' + $panelParentId + '_' + $panelId).find('input[type=checkbox]').each(function () {
                    @this.removeItem($(this).val());
                    });
                } else {
                @this.addItem($value);
                }
            });
        });
    </script>
@endpush
