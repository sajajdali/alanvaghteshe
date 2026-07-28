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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();

            // وابستگی اختیاری به کاربر سیستم شما
            $table->foreignIdFor(\Modules\User\Entities\User::class)
                ->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(\Modules\Admin\app\Models\WhatsappSession::class)->nullable()->constrained()->nullOnDelete();

            // شناسه‌ها و کانتکست گفتگو
            $table->string('session_name')->index();       // مثلا: sajjad_irancell
            $table->string('chat_id')->index();           // مخاطب مقابل: 9891...@c.us
            $table->string('sender_id')->index();         // فرستنده
            $table->boolean('from_me')->default(false)->index();

            // کلید یکتا برای جلوگیری از ثبت تکراری (اگر از webhook/SDK می‌آید)
            $table->string('wa_message_key')->nullable()->unique(); // مثلا _data.id._serialized

            // محتوا و وضعیت
            $table->enum('type', ['text','image','video','audio','document','sticker','unknown'])
                ->default('unknown')->index();
            $table->text('body')->nullable();              // متن/کپشن (بدون باینری/بیس64)
            $table->timestamp('read_at')->nullable();

            // خلاصه‌ی رسانه
            $table->boolean('has_media')->default(false)->index();

            $table->timestamps();

            // ایندکس‌های پرکاربرد
            $table->index(['session_name','chat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
