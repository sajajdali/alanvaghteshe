<?php
namespace Modules\Api\Emails;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RegisterMail extends Mailable implements ShouldQueue
{
    use Dispatchable , InteractsWithQueue , Queueable, SerializesModels ;

    public $verification_code;

    /**
     * @param $verification_code
     */
    public function __construct( $verification_code)
    {
        $this->verification_code = $verification_code;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('verification code')
            ->from('no-reply@alanvaghteshe.com')
            ->view('api::mail.register')->with([
                'code'  => $this->verification_code,
            ]);
    }
}
