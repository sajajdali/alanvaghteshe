<?php

use Modules\Setting\Livewire\Admin\Setting\Setting;

Route::get('/setting', Setting::class)->name('setting')->can('viewAny', \Modules\Setting\Entities\Setting::class);
