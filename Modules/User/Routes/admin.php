<?php

//user routes
Route::get('user', \Modules\User\Livewire\Admin\User\UserList::class)->name('user.index')->can('viewAny', \Modules\User\Entities\User::class);
Route::get('user/referral', \Modules\User\Livewire\Admin\User\ReferralList::class)->name('user.referral.index')->can('viewAny', \Modules\User\Entities\User::class);
Route::get('user/create', \Modules\User\Livewire\Admin\User\UserCreateOrUpdate::class)->name('user.create')->can('create', \Modules\User\Entities\User::class);
Route::get('user/edit/{user}', \Modules\User\Livewire\Admin\User\UserCreateOrUpdate::class)->name('user.edit');//access should check in liviwire component edit part
Route::get('user/documents/{user}', \Modules\User\Livewire\Admin\User\UserDocuments\Index::class)->name('user.document');
Route::get('user/documents/assign-diet/{user}', \Modules\User\Livewire\Admin\User\AssignDiet::class)->name('user.assign.diet');
Route::get('user/documents/assign-package/{user}', \Modules\User\Livewire\Admin\User\AssignPackage::class)->name('user.assign.package');
Route::get('user/complete_registration/{user}', \Modules\User\Livewire\Admin\User\CompleteRegistration::class)->name('user.complete_registration');//access should check in liviwire component edit part

Route::get('user/documents/edit-package/{user}/package/{packageUser}', \Modules\User\Livewire\Admin\User\EditPackage::class)->name('user.assign.edit');
//role routes
Route::get('role', \Modules\User\Livewire\Admin\Role\RoleList::class)->name('role.index')->can('viewAny', \Spatie\Permission\Models\Role::class);
Route::get('role/create', \Modules\User\Livewire\Admin\Role\RoleCreateOrUpdate::class)->name('role.create')->can('create', \Spatie\Permission\Models\Role::class);
Route::get('role/edit/{role}', \Modules\User\Livewire\Admin\Role\RoleCreateOrUpdate::class)->name('role.edit');//access should check in liviwire component edit part
Route::get('user/telegram_create_access/{user}', function (\Modules\User\Entities\User $user) {
    $user->telegram_chat_id = $user->invitation_code;
    session()->flash('success', 'کد دسترسی به ربات با موفقیت ایجاد شد');
    return redirect()->back();
})->name('user.telegram.create')->can('create', \Modules\User\Entities\User::class);
