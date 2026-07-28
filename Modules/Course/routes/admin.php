<?php

use Modules\Course\Livewire\Admin\Course\CourseAppBannerCreateOrUpdate;
use Modules\Course\Livewire\Admin\Course\CourseAppBannerList;
use Modules\Course\Livewire\Admin\Course\CourseCategoryManager;
use Modules\Course\Livewire\Admin\Course\CourseCreateOrUpdate;
use Modules\Course\Livewire\Admin\Course\CourseCommentManager;
use Modules\Course\Livewire\Admin\Course\CourseContentManager;
use Modules\Course\Livewire\Admin\Course\CourseFaqManager;
use Modules\Course\Livewire\Admin\Course\CourseList;
use Modules\Course\Livewire\Admin\Course\CoursePaymentList;
use Modules\Course\Livewire\Admin\Course\CoursePurchaseDetail;
use Modules\Course\Livewire\Admin\Course\CoursePurchasedList;
use Modules\Course\app\Models\Course;

Route::get('course', CourseList::class)->name('course.index')->can('viewAny', Course::class);
Route::get('course/create', CourseCreateOrUpdate::class)->name('course.create')->can('create', Course::class);
Route::get('course/categories', CourseCategoryManager::class)->name('course.categories')->can('viewAny', Course::class);
Route::get('course/faqs', CourseFaqManager::class)->name('course.faqs.index')->can('viewAny', Course::class);
Route::get('course/edit/{course}', CourseCreateOrUpdate::class)->name('course.edit');
Route::get('course/{course}/content', CourseContentManager::class)->name('course.content');
Route::get('course/{course}/faqs', CourseFaqManager::class)->name('course.faqs');
Route::get('course/{course}/comments', CourseCommentManager::class)->name('course.comments');
Route::get('course/banners', CourseAppBannerList::class)->name('course.banners.index')->can('viewAny', Course::class);
Route::get('course/banners/create', CourseAppBannerCreateOrUpdate::class)->name('course.banners.create')->can('create', Course::class);
Route::get('course/banners/edit/{banner}', CourseAppBannerCreateOrUpdate::class)->name('course.banners.edit');
Route::get('course/purchased/list', CoursePurchasedList::class)->name('course.purchased');
Route::get('course/purchased/{purchase}', CoursePurchaseDetail::class)->name('course.purchased.show');
Route::get('course/payments/list', CoursePaymentList::class)->name('course.payments');


