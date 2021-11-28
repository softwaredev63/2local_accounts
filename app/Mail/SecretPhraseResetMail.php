<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SecretPhraseResetMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $username;
    public $secretPhrase;

    public function __construct(string $username, string $secretPhrase)
    {
        $this->username = $username;
        $this->secretPhrase = $secretPhrase;
    }

    public function build(): SecretPhraseResetMail
    {
        return $this->from(config('mail.from'))
            ->subject('Secret Phrase Reset')
            ->view('emails.secret_phrase_reset');
    }
}
