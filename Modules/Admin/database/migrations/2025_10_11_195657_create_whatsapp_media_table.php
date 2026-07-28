<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')
                ->constrained('whatsapp_messages')
                ->cascadeOnDelete();

            // نوع و ویژگی‌های فایل
            $table->enum('kind', ['image','video','audio','document','sticker','unknown'])
                ->default('unknown')->index();

            // محل ذخیره روی استوریج خودت
            // مثال: storage/app/public/wa/2025/10/xyz.mp4
            $table->string('disk')->nullable();                 // مثلا public/s3/...
            $table->string('path');                             // مسیر نسبی روی disk
            $table->string('original_filename')->nullable();    // نام اصلی فایل
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable();     // بایت

            // ابعاد/مدت برای تصویر/ویدئو/صوت (در صورت نیاز)
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();

            // اگر لازم شد لینک موقت/دایمی بدهی (اختیاری)
            $table->text('public_url')->nullable();

            $table->timestamps();

            // برای جلوگیری از تکرار یک فایل روی یک پیام
            $table->unique(['message_id','path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_media');
    }
};
