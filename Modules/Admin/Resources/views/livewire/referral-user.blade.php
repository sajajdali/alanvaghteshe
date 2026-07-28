<div>
    <!-- صفحه اصلی -->
    @if($page == 'not_found')
        <section class="w-full min-h-screen flex flex-col py-14">
            <div class="hidden md:flex justify-end mb-10">
                <a href="http://webapp.zood.fit" class="flex items-center gap-4 text-white">
                    <p class="font-casa font-bold text-2xl">بازگشت</p>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 9l-3 3m0 0l3 3m-3-3h7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </a>
            </div>
            <div class="w-full flex-grow flex flex-col">
                <svg class="w-28 h-28 text-rose-600 mx-auto" xmlns="http://www.w3.org/2000/svg">
                    <use xlink:href="../assets/svg/icon.svg#sprite-failed" />
                </svg>
                <h4 class="text-rose-600 text-center text-2xl mt-8">
                    آدرس وارد شده یافت نشد
                </h4>
                <div class="mt-8 flex justify-center">
                    <a href="http://webapp.zood.fit" class="flex items-center gap-4 py-4 px-6 lg:py-3 lg:px-4 border border-purple-600 bg-purple-600 text-white rounded-full hover:bg-transparent hover:text-purple-600 transition-all">
                        <p class="text-sm transition-none">بازگشت</p>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>

    @elseif($page == 'referral')
        <section class="w-full min-h-screen flex flex-col justify-center items-center">
            <div class="flex flex-col lg:flex-row items-center gap-6 py-10">
                <div class="w-full lg:w-[40%]">
                    <h2 class="block lg:hidden text-center text-xl text-purple-600 my-7">
                       "الان وقتشه"
                        سبک زندگیت رو عوض کنی
                    </h2>
                        <img src="{{asset('assets/landing/banner.png')}}" alt="تصویر صفحه اصلی" class="w-[90%] mx-auto" />
                </div>
                <div class="w-full lg:w-[60%]">
                    <h2 class="hidden lg:block text-center lg:text-right text-xl text-purple-600 my-6">
                       الان وقت تناسب اندامه
                    </h2>
                    <h1 class="text-center lg:text-right font-bold text-2xl lg:text-3xl my-10">
                        <span class=" text-4xl text-rose-600 font-casa">{{$user->full_name}}</span>
                        <br>
                     انتخاب کرده سالم تر زندگی کنه
                        <br>
                            و تو رو هم به این انتخاب دعوت کرده
                    </h1>
                    <p class="text-center lg:text-justify text-sm leading-[1.65rem] text-black mb-8">
                        "الان وقتشه"
                        فقط یک اپ نیست، یه همراه قدرتمنده که کمکت میکنه سبک زندگیتو سالم تر و هدفمندتر کنی. با ترکیب علم تغذیه و تکنولوژی، مسیرتو هموار میکنیم تا به بهترین نسخه خودت برسی
                    </p>
                    <div class="flex flex-wrap flex-col lg:flex-row items-center gap-4">
                        <p class="text-center lg:text-right text-sm font-bold leading-7">
                           برای شروع کلیک کنید
                        </p>

                        <!-- کانتینر اصلی -->
                        <div class="flex flex-wrap flex-col lg:flex-row items-center gap-4 pb-24 lg:pb-0">

                            <!-- دکمه فقط در دسکتاپ: عادی -->
                            <div class="hidden lg:block">
                                <button wire:click="changePage('enter_mobile')" class="btn_purple">
                                    ثبت نام
                                </button>
                            </div>
                        </div>

                        <!-- دکمه فقط در موبایل: پایین صفحه ثابت -->
                        <div class="block lg:hidden">
                            <div class="mobile-fixed-btn-wrapper">
                                <button wire:click="changePage('enter_mobile')" class="mobile-fixed-btn">
                                    ثبت نام
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @elseif($page == 'enter_mobile')

        <!-- صفحه وارد کردن شماره -->
        <section class="w-full min-h-screen flex flex-col py-14">
            <div class="hidden md:flex justify-end">
                <a href="#" class="flex items-center gap-4 text-white">
                    <p class="font-casa font-bold text-2xl">بازگشت</p>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 9l-3 3m0 0l3 3m-3-3h7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </a>
            </div>
            <div class="w-full flex-grow flex flex-col items-center justify-center">
                <div class="w-full max-w-[500px] bg-white rounded-xl shadow-lg shadow-gray-300 py-14 px-6 lg:px-14">
                    <p class="text-center text-xl font-bold text-black">
                        لطفا شماره تلفن خود را وارد کنید
                    </p>

                    <!-- فرم موبایل -->
                    @if($loginMethod === 'mobile')
                        <div class="w-full flex items-center justify-center gap-3 mt-10">
                            <button wire:click="sendCode"
                                    class="flex items-center gap-4 py-4 px-6 lg:py-3 lg:px-4 border border-purple-600 bg-purple-600 text-white rounded-full hover:bg-transparent hover:text-purple-600 transition-all"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="bg-purple-400 cursor-not-allowed">
                <span wire:loading.remove>
                    <p class="text-sm transition-none">ثبت</p>
                </span>
                                <span wire:loading>
                    <svg xmlns="http://www.w3.org/2000/svg" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 1116 0 8 8 0 01-16 0z"></path>
                    </svg>
                </span>
                            </button>
                            <div class="flex-grow flex items-center bg-gray-100 rounded-full py-4 px-6 gap-2">
                                <input type="text"
                                       class="text-left pl-1 flex-grow border-none outline-none bg-transparent"
                                       autofocus dir="ltr"
                                       wire:model="mobile"
                                       placeholder="برای مثال 0912000000"
                                       id="txtPhoneNumber"
                                       maxlength="12" />
                            </div>
                        </div>
                    @elseif($loginMethod === 'email')
                        <!-- فرم ایمیل -->
                        <div class="w-full flex items-center justify-center gap-3 mt-10">
                            <button wire:click="sendCode"
                                    class="flex items-center gap-4 py-4 px-6 lg:py-3 lg:px-4 border border-purple-600 bg-purple-600 text-white rounded-full hover:bg-transparent hover:text-purple-600 transition-all"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="bg-purple-400 cursor-not-allowed">
                <span wire:loading.remove>
                    <p class="text-sm transition-none">ثبت</p>
                </span>
                                <span wire:loading>
                    <svg xmlns="http://www.w3.org/2000/svg" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 1116 0 8 8 0 01-16 0z"></path>
                    </svg>
                </span>
                            </button>
                            <div class="flex-grow flex items-center bg-gray-100 rounded-full py-4 px-6 gap-2">
                                <input type="email"
                                       class="text-left pl-1 flex-grow border-none outline-none bg-transparent"
                                       autofocus dir="ltr"
                                       wire:model="email"
                                       placeholder="برای مثال example@example.com"
                                       id="txtEmail"
                                       maxlength="100" />
                            </div>
                        </div>
                    @endif

                    @error('mobile')
                    <h4 class="text-rose-600 text-center text-2xl mt-8">
                        {{ $message }}
                    </h4>
                    @enderror
                    @error('email')
                    <h4 class="text-rose-600 text-center text-2xl mt-8">
                        {{ $message }}
                    </h4>
                    @enderror

                    <!-- لینک تغییر فرم ورود -->
                    <div class="mt-4 text-center">
                        <a href="javascript:void(0);" wire:click="toggleLoginMethod" class="text-purple-600 font-bold hover:text-purple-800 text-sm">
                            ورود با {{ $loginMethod === 'mobile' ? 'ایمیل' : 'موبایل' }}
                        </a>
                    </div>
                </div>

                <div class="flex md:hidden justify-center mt-10">
                    <a href="#" class="flex items-center gap-4 text-purple-600">
                        <p class="font-casa font-bold text-2xl">بازگشت</p>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 9l-3 3m0 0l3 3m-3-3h7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>

    @elseif($page == 'redirect_to_app')
        <div id="redirect_to_app">
        <section  class="w-full min-h-screen flex flex-col py-14">
            <div class="hidden md:flex justify-end">
                <a href="#" class="flex items-center gap-4 text-white">
                    <p class="font-casa font-bold text-2xl">بازگشت</p>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 9l-3 3m0 0l3 3m-3-3h7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </a>
            </div>
            <div class="w-full min-h-screen flex flex-col items-center justify-center">
                <div class="w-full max-w-[500px] bg-white rounded-xl shadow-lg shadow-gray-300 py-14 px-6 lg:px-14">
                    <p class="text-center text-xl font-bold text-black">
                        برای ورود به پروفایل روی کلید زیر بزنید
                    </p>
                    <br><br>
                    <div class="buttons-container">
                        <!-- دکمه ورود به وب اپلیکیشن -->
                        <a href="{{$applicationLink}}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-4 py-4 px-6 lg:py-3 lg:px-4 border border-purple-600 bg-purple-600 text-white rounded-full hover:bg-transparent hover:text-purple-600 transition-all">
                            <p class="text-center transition-none">ورود به وب اپلیکیشن</p>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                            </svg>
                        </a>

                        <!-- دکمه دانلود نسخه اندروید -->

                        <button class="flex items-center gap-4 py-4 px-6 lg:py-3 lg:px-4 border border-green-600 bg-green-600 text-white rounded-full hover:bg-transparent hover:text-green-600 transition-all" data-bs-toggle="modal" data-bs-target="#androidModal">
                            دانلود نسخه اندروید
                        </button>

                        <!-- دکمه برای باز کردن Modal iOS -->
                        <button class="flex items-center gap-4 py-4 px-6 lg:py-3 lg:px-4 border border-blue-600 bg-blue-600 text-white rounded-full hover:bg-transparent hover:text-blue-600 transition-all" data-bs-toggle="modal" data-bs-target="#iosModal">
                            دانلود نسخه iOS
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal Android -->
        <div class="modal fade" id="androidModal" tabindex="-1" aria-labelledby="androidModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="androidModalLabel">از کجا می‌خواهید نسخه اندروید را دانلود کنید؟</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-around gap-10">
                            <button class="btn btn-success" onclick="window.location.href='https://cafebazaar.ir/app/com.alanvaghteshe'">
                               دانلود از کافه بازار
                            </button>
                            <button class="btn btn-primary" onclick="window.location.href='https://webapp.alanvaghteshe.com/apk/alanvaghteshe.apk'">
                                دانلود مستقیم
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- پاپ‌آپ دانلود iOS -->
        <div class="modal fade" id="iosModal" tabindex="-1" aria-labelledby="iosModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="iosModalLabel">از کجا می‌خواهید نسخه iOS را دانلود کنید؟</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-around">
                            <button class="btn btn-info" onclick="window.location.href='https://sibapp.com/applications/Alanvaghteshe'">
                                سیبچه
                            </button>
                            <button class="btn btn-primary" onclick="window.location.href='https://iapps.ir/app/%D8%A7%D9%84%D8%A7%D9%86-%D9%88%D9%82%D8%AA%D8%B4%D9%87/707404381'">
                                iapps
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    @elseif($page == 'enter_code')
        <section class="w-full min-h-screen flex flex-col py-14">
            <div class="hidden md:flex justify-end">
                <a href="#" class="flex items-center gap-4 text-white">
                    <p class="font-casa font-bold text-2xl">بازگشت</p>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 9l-3 3m0 0l3 3m-3-3h7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </a>
            </div>
            <div class="w-full flex-grow flex flex-col items-center justify-center">
                <div class="w-full max-w-[500px] bg-white rounded-xl shadow-lg shadow-gray-300 py-14 px-6 lg:px-14">
                    <p class="text-center text-xl font-bold text-black">
                        کد ارسال شده  را وارد کنید
                    </p>
                    <form class="w-full flex flex-col items-center justify-center mt-10 ">
                        <div x-data="{
         code: ['', '', '', ''],
         focusNext(index) {
             if (index < 3 && this.code[index].length === 1) {
                 this.$nextTick(() => {
                     this.$refs[`code${index + 1}`].focus();
                 });
             } else if (index > 0 && this.code[index].length === 0) {
                 this.$nextTick(() => {
                     this.$refs[`code${index - 1}`].focus();
                 });
             }
         },
         timer: 120,
         interval: null,
         startTimer() {
             this.interval = setInterval(() => {
                 if (this.timer > 0) {
                     this.timer--;
                 } else {
                     clearInterval(this.interval);
                     this.showResendButton = true;
                 }
             }, 1000);
         },
         resetTimer() {
             this.timer = 120;
             this.showResendButton = false;
             clearInterval(this.interval);
             this.startTimer();
         },
         showResendButton: false
     }" x-init="startTimer(); $nextTick(() => { $refs.code0.focus(); })">
                            <div dir="ltr" class="w-full flex items-center justify-center gap-3 lg:gap-5">
                                <!-- فیلد شماره 1 -->
                                <div class="w-14 h-14 bg-gray-200 rounded-full flex items-center justify-center">
                                    <input type="number"
                                           x-model="code[0]"
                                           wire:model="number.0"
                                           x-ref="code0"
                                           class="txtCode w-10 border-none outline-none bg-transparent text-gray-700 text-center text-lg font-bold"
                                           id="code1"
                                           x-on:input="focusNext(0)"
                                           maxlength="1" />
                                </div>
                                <!-- فیلد شماره 2 -->
                                <div class="w-14 h-14 bg-gray-200 rounded-full flex items-center justify-center">
                                    <input type="number"
                                           wire:model="number.1"
                                           x-model="code[1]"
                                           x-ref="code1"
                                           class="txtCode w-10 border-none outline-none bg-transparent text-gray-700 text-center text-lg font-bold"
                                           id="code2"
                                           x-on:input="focusNext(1)"
                                           maxlength="1" />
                                </div>
                                <!-- فیلد شماره 3 -->
                                <div class="w-14 h-14 bg-gray-200 rounded-full flex items-center justify-center">
                                    <input type="number"
                                           x-model="code[2]"
                                           wire:model="number.2"
                                           x-ref="code2"
                                           class="txtCode w-10 border-none outline-none bg-transparent text-gray-700 text-center text-lg font-bold"
                                           id="code3"
                                           x-on:input="focusNext(2)"
                                           maxlength="1" />
                                </div>
                                <!-- فیلد شماره 4 -->
                                <div class="w-14 h-14 bg-gray-200 rounded-full flex items-center justify-center">
                                    <input type="number"
                                           x-model="code[3]"
                                           wire:model.*="number.3"
                                           x-ref="code3"
                                           class="txtCode w-10 border-none outline-none bg-transparent text-gray-700 text-center text-lg font-bold"
                                           id="code4"
                                           maxlength="1" />
                                </div>

                            </div>

                            <p class="text-center text-lg mt-4" x-text="`${String(timer).padStart(2, '0')}`"></p>
                            @if(isset($sendAgain))
                                <h4 class="text-rose-600 text-center text-2xl mt-8">
                                    {{ $sendAgain }}
                                </h4>
                            @endif
                            <div x-show="showResendButton" class="sendagain_container show mt-8">
                                <button type="button" class="sendagain_button" x-on:click="resetTimer()" wire:click="sendCodeAgain">
                                    ارسال مجدد کد
                                </button>
                            </div>
                        </div>
                        <button type="button" wire:click="submitCode()"
                                class="flex items-center gap-4 py-4 px-6 lg:py-3 lg:px-4 border border-purple-600 bg-purple-600 text-white rounded-full hover:bg-transparent hover:text-purple-600 transition-all"
                                wire:loading.attr="disabled"
                                wire:loading.class="bg-purple-400 cursor-not-allowed">
                            <!-- انیمیشن لودینگ -->
                            <span wire:loading.remove>
                                <p class="text-sm transition-none">تایید و ثبت</p>
                            </span>
                            <span wire:loading>
                                <svg xmlns="http://www.w3.org/2000/svg" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 1116 0 8 8 0 01-16 0z"></path>
                                </svg>
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </section>


    @endif

    <!-- سوالات متداول -->
</div>
@push('scripts')
    <script>
        // برای نمایش Modal اندروید
        $('#androidModal').modal('show');

        // برای نمایش Modal iOS
        $('#iosModal').modal('show');
        // توابع اختصاصی تایمر و ورود عدد فارسی به انگلیسی در صورت نیاز
        document.getElementById('txtPhoneNumber').addEventListener('input', function (event) {
            let persianNumbers = ["۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹"];
            let englishNumbers = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];

            let inputValue = event.target.value;

            inputValue = inputValue.replace(/[۰-۹]/g, function (match) {
                return englishNumbers[persianNumbers.indexOf(match)];
            });

            event.target.value = inputValue;
        });
    </script>
@endpush
@push('styles')
    <style>
        .modal-dialog {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .modal-body .d-flex {
            gap: 10px; /* فاصله بین دکمه‌ها */
        }
        button.bg-purple-600:hover {
            background: linear-gradient(135deg, #2f215d, #734689) !important;   /* رنگ متفاوت هنگام هاور */
        }

        .bg-purple-600:hover {
            background: linear-gradient(135deg, #2f215d, #734689) !important;
        }

        button.bg-green-600:hover {
            background: linear-gradient(135deg, #2F9A75, #0F8C60);  /* رنگ متفاوت هنگام هاور */
        }

        button.bg-blue-600:hover {
            background: linear-gradient(135deg, #1E4AB5, #0F3585);  /* رنگ متفاوت هنگام هاور */
        }
        /* کانتینر دکمه‌ها */
        .buttons-container {
            display: flex;
            flex-direction: column;
            gap: 20px;  /* فاصله بین دکمه‌ها */
            align-items: center; /* دکمه‌ها را وسط چین می‌کند */
            padding: 20px;
        }

        /* دکمه‌ها */
        button {
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: bold;
            font-size: 1rem;
            text-transform: uppercase;
            color: white;
            border: none;
            width: 100%;  /* دکمه‌ها عرض کامل دارند */
            max-width: 300px; /* حداکثر عرض برای دکمه‌ها */
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* دکمه ورود به وب اپلیکیشن */
        button.bg-purple-600 {
            background: linear-gradient(135deg, #6f42c1, #9b59b6);
        }

        button.bg-purple-600:hover {
            background: linear-gradient(135deg, #2f215d, #734689);
        }

        /* دکمه دانلود نسخه اندروید */
        button.bg-green-600 {
            background: linear-gradient(135deg, #34D399, #10B981);
        }

        button.bg-green-600:hover {
            background: linear-gradient(135deg, #2F9A75, #0F8C60);
        }

        /* دکمه دانلود نسخه iOS */
        button.bg-blue-600 {
            background: linear-gradient(135deg, #3B82F6, #2563EB);
        }

        button.bg-blue-600:hover {
            background: linear-gradient(135deg, #1E4AB5, #0F3585);
        }

        /* آیکون دکمه‌ها */
        button svg {
            width: 24px;
            height: 24px;
            transition: transform 0.3s;
        }

        /* تغییر سایز آیکون هنگام هاور */
        button:hover svg {
            transform: scale(1.2);
        }

        /* حالت لودینگ */
        button:disabled {
            background-color: #e5e7eb;
            color: #9ca3af;
            cursor: not-allowed;
        }

        /* برای موبایل: دکمه‌ها در پایین صفحه */
        @media (max-width: 768px) {
            button {
                width: 100%;
            }
        }
    </style>
@endpush
