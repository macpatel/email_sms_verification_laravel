<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Crypt;
use App\Models\EmailVerification;

class EmailVerificationLink extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var EmailVerification
     */
    protected $emailverification;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(EmailVerification $emailverification)
    {
        $this->emailverification = $emailverification;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.email_verification')
                    ->with([
                        'encryptedEmailId' => Crypt::encryptString($this->emailverification->email_id),
                        'verificationCode' => $this->emailverification->verification_code,
                    ]);
    }
}
