<?php

namespace Modules\Admin\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[title('ورود')]
#[Layout('admin::layouts.login')]
class Login extends Component
{
    #[Rule('required',message: 'آدرس ایمیل را وارد کنید')]
    #[Rule('email',message: 'ادرس ایمیل به درستی وارد نشده است')]
    public $email;
    #[Rule('required',message: 'رمز عبور را وارد کنید')]
    public $password;

    public $recaptcha;

    public $message;

    public function mount()
    {
        if (auth()->check() && auth()->user()->can('ADMIN_ACCESS')) {
            return redirect()->route('admin.dashboard');
        }
        if (auth()->check()) {
            return redirect()->route('profile.dashboard');
        }
    }

    public function requestLogin()
    {
        $this->message = '';
        $this->validate();

        if ($this->isLocalRequest()) {
            if (auth()->attempt(['email' => $this->email, 'password' => $this->password], true)) {
                return redirect()->route('admin.dashboard');
            }

            $this->message = 'ایمیل یا رمز عبور اشتباه است';
            return;
        }

        $recaptcha = new \ReCaptcha\ReCaptcha(config('app.recaptcha.secret_key'));
        $resp = $recaptcha
            ->setExpectedAction('login')
            ->verify($this->recaptcha, request()->ip());
        if ($resp->isSuccess()) {
            if (auth()->attempt(['email' => $this->email, 'password' => $this->password], true)) {
                return redirect()->route('admin.dashboard');
            }
            $this->message = 'ایمیل یا رمز عبور اشتباه است';
        } else {
            $this->message = 'خطا در سرور! مجدد تلاش کنید.';
        }
        $this->dispatch('resetReCaptcha');
    }

    protected function isLocalRequest(): bool
    {
        return app()->environment('local') || in_array(request()->ip(), ['127.0.0.1', '::1']);
    }

    public function render()
    {
        return view('admin::livewire.login');
    }
}
