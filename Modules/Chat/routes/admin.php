<?php
Route::get('chat', \Modules\Chat\Livewire\ChatView::class)->name('chat')->can('viewAny', \Modules\Chat\app\Models\Chat::class);
