<?php

use Modules\Package\Entities\Package;
use Modules\Package\Livewire\Admin\PackageList;
use Modules\Package\Livewire\Admin\PackageUpdateOrCreate;
use Modules\Package\Livewire\Admin\PurchasedPackage;

Route::get('package', PackageList::class)->name('package.index')->can('viewAny',
    Package::class);
Route::get('package/create', PackageUpdateOrCreate::class)->name('package.create')->can('create', Package::class);
Route::get('package/edit/{package}', PackageUpdateOrCreate::class)->name('package.edit');
Route::get('package/purchased', PurchasedPackage::class)->name('package.purchased');
