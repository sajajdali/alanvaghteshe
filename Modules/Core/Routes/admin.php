<?php
//faq
Route::get('faq', \Modules\Core\Livewire\Admin\FaqLiveWire::class)->name('faq')->can('viewAny', \Modules\Core\Entities\Faq::class);
