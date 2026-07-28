<div>
    <!-- PAGE -->
    <div class="page">
        <div>
            <!-- CONTAINER OPEN -->
            <div class="col col-login mx-auto text-center">
                <a href="{{url('index')}}" class="text-center">
{{--                    <img src="{{asset('assets/images/brand/logo.png')}}" class="header-brand-img" alt="">--}}
                </a>
            </div>
            <div class="container-login100">
                <div class="wrap-login100 p-0">
                    <div class="card-body">
                        @if($status == 'test')
                            <div>
                                <div class="alert alert-info" role="alert">
                                    <span class="alert-inner--text"> اتصال به درگاه مقدور نیست</span>
                                </div>
                                <a href="https://webapp.alanvaghteshe.com/transaction/{{$transaction->id}}"
                                   class="login100-form-btn btn-primary">
                                    بازگشت به اپلیکیشن
                                </a>
                            </div>
                        @elseif($status == 'showMessage')
                            <div>
                                <div class="alert alert-info" role="alert">
                                    <span class="alert-inner--text"> {{$message}}</span>
                                </div>
                                <a href="https://webapp.alanvaghteshe.com/transaction/{{$transaction->id}}"
                                   class="login100-form-btn btn-primary">
                                    بازگشت به اپلیکیشن
                                </a>
                            </div>
                        @elseif($status == 'successful')
                            <div>
                                <div class="alert alert-success" role="alert">
                                    <span class="alert-inner--text"> پرداخت شما با موفقیت انجام شد</span>
                                </div>
                                <a href="https://webapp.alanvaghteshe.com/transaction/{{$transaction->id}}"
                                   class="login100-form-btn btn-primary">
                                    بازگشت به اپلیکیشن
                                </a>
                            </div>
                        @elseif($status == 'paypal')
                            <div>
                                <div class="alert alert-success" role="alert">
                                    <span class="alert-inner--text"> لطفا مبلغ </span>
                                    16$
                                    <br>
                                    را به حساب paypal
                                    به آدرس ایمیل
                                    <br>
                                    dr@alanvaghteshe.com
                                    <br>
                                    واریز و سپس ایمیل خود را در پشتیبانی ثبت کنید تا پکیج و رژیم برای شما فعال شود.

                                </div>
                                <a href="https://webapp.alanvaghteshe.com/support"
                                   class="login100-form-btn btn-primary">
                                    بازگشت به اپلیکیشن
                                </a>
                            </div>
                        @elseif($status == 'failed')
                            <div>
                                <div class="alert alert-danger" role="alert">
                                    <span class="alert-inner--text"> پرداخت انجام نشد . درصورت کم شدن مبلغ از حساب شما ، حداکثر تا ۴۸ ساعت ایده به حساب شما بازگشت داده خواهد شد.

                                    <br>
                                        در صورتی که پس از ۴۸ ساعت بازگشت انجام نشد ، لطفا به پشتیبانی اطلاع دهید
                                    </span>
                                    <br><br>
                                    <spa>
                                        شناسه پرداخت :
                                        {{$transaction->id}}
                                    </spa>
                                </div>

                                <div class="container-login100-form-btn">
                                    <button type="button" class="login100-form-btn btn-success"
                                            wire:loading.class="bg-gray btn-loading disabled"
                                            wire:click="orderAgain"
                                    >
                                        پرداخت مجدد
                                    </button>
                                </div>
                            </div>
                        @else
                            <form class="login100-form validate-form ">
                                <div class="text-center mb-4">
                                    <img src="{{asset('default/admin/logo.jpeg')}}" alt="lockscreen image"
                                         class="avatar avatar-xxl brround mb-2">
                                    <h4>پرداخت آنلاین</h4>
                                </div>
                                <div>
                                    <div class="alert alert-info" role="alert">
                                        <span class="alert-inner--text">روش پرداخت رو انتخاب کن</span>
                                    </div>

                                </div>

                                <div class="container-login100-form-btn">
                                    <button type="button" class="login100-form-btn btn-success"
                                            wire:loading.class="bg-gray btn-loading disabled"
                                            wire:click="onlinePayment"
                                    >
                                        پرداخت داخل ایران
                                    </button>
                                </div>
{{--                                <div class="container-login100-form-btn">--}}
{{--                                    <button type="button" class="login100-form-btn btn-danger"--}}
{{--                                            wire:loading.class="bg-gray btn-loading disabled"--}}
{{--                                            wire:click="paymentFailed">--}}
{{--                                        پرداخت نا موفق--}}
{{--                                    </button>--}}
{{--                                </div>--}}
                                <br>
                                <div class="container-login100-form-btn">
                                <button type="button" wire:click="paymentDollar"  class="login100-form-btn p-2 btn-light">پرداخت خارج از ایران</button>
                                </div>
                                <div class="text-center pt-2">
                                    <!-- <span class="txt1">
                                        I Forgot
                                    </span> -->
                                    <a href="https://webapp.alanvaghteshe.com/transaction/{{$transaction->id}}"
                                       class="login100-form-btn btn-warning">
                                        بازگشت به اپلیکیشن
                                    </a>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            <!-- CONTAINER CLOSED -->
        </div>
    </div>
    <!-- End PAGE -->
</div>
{{--@push('scripts')--}}
{{--    <script src="https://www.google.com/recaptcha/api.js?render={{ config('app.recaptcha.site_key') }}"></script>--}}

{{--    <script>--}}
{{--        function resetCaptcha() {--}}
{{--            grecaptcha.ready(function () {--}}
{{--                grecaptcha.execute('{{ config('app.recaptcha.site_key') }}', {action: 'login'}).then(function (token) {--}}
{{--                @this.set('recaptcha', token)--}}
{{--                    ;--}}
{{--                });--}}
{{--            });--}}
{{--        }--}}

{{--        Livewire.on('resetReCaptcha', () => {--}}
{{--            resetCaptcha();--}}
{{--        });--}}

{{--        $(document).ready(function () {--}}
{{--            resetCaptcha();--}}
{{--        });--}}
{{--    </script>--}}
{{--@endpush--}}
