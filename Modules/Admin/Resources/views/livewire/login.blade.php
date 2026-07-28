<div>
    <!-- PAGE -->
    <div class="page">
        <div>
            <!-- CONTAINER OPEN -->
            <div class="col col-login mx-auto text-center">
                <a href="{{url('index')}}" class="text-center">
                    <img src="{{asset('assets/images/brand/logo.png')}}" class="header-brand-img" alt="">
                </a>
            </div>
            <div class="container-login100">
                <div class="wrap-login100 p-0">
                    <div class="card-body">
                        <form class="login100-form validate-form" wire:submit="requestLogin">
									<span class="login100-form-title">
										ورود
									</span>
                            @if (!empty($message))
                                <div class="alert alert-danger">
                                    {{ $message }}
                                </div>
                            @endif
                            <div class="wrap-input100 validate-input"
                                 data-bs-validate="آدرس ایمیل الزامی است">
                                <input class="input100" type="text" name="email" placeholder="آدرس ایمیل"
                                       wire:model="email">
                                <span class="focus-input100"></span>
                                <span class="symbol-input100">
											<i class="zmdi zmdi-email" aria-hidden="true"></i>
										</span>
                            </div>
                            @error('email')
                            <div class="invalid-feedback" style="display: block;margin-top: 0;margin-bottom: 4px">
                                {{ $message }}
                            </div>
                            @enderror
                            <div class="wrap-input100 validate-input" data-bs-validate="کلمه عبور الزامی است">
                                <input class="input100" type="password" name="pass" placeholder="کلمه عبور"
                                       wire:model="password">
                                <span class="focus-input100"></span>
                                <span class="symbol-input100">
											<i class="zmdi zmdi-lock" aria-hidden="true"></i>
										</span>
                            </div>
                            @error('password')
                            <div class="invalid-feedback" style="display: block;margin-top: 0;margin-bottom: 4px">
                                {{ $message }}
                            </div>
                            @enderror
                            <div class="text-end pt-1">
                                <p class="mb-0"><a href="{{url('forgot-password')}}" class="text-primary ms-1">پسورد
                                        خود
                                        را فراموش کرده‌اید؟</a></p>
                            </div>

                            <div class="container-login100-form-btn">
                                <button type="submit" class="login100-form-btn btn-primary" wire:loading.class="bg-gray btn-loading disabled">
                                    ورود
                                </button>
                            </div>

                            <div class="text-center pt-3">
                                <p class="text-dark mb-0">کاربر سایت هستید؟<a href="{{url('register')}}"
                                                                              class="text-primary ms-1">ورود به بخش
                                        کاربری</a>
                                </p>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
            <!-- CONTAINER CLOSED -->
        </div>
    </div>
    <!-- End PAGE -->
</div>
@push('scripts')
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('app.recaptcha.site_key') }}"></script>

    <script>
        function resetCaptcha() {
            grecaptcha.ready(function() {
                grecaptcha.execute('{{ config('app.recaptcha.site_key') }}', {action: 'login'}).then(function(token) {
                @this.set('recaptcha', token);
                });
            });
        }

        Livewire.on('resetReCaptcha', () => {
            resetCaptcha();
        });

        $(document).ready(function () {
            resetCaptcha();
        });
    </script>
@endpush
