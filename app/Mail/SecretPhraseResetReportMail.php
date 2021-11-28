<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SecretPhraseResetReportMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $resetResults;

    public function __construct(array $resetResults)
    {
        $this->resetResults = $resetResults;
    }

    public function build(): SecretPhraseResetReportMail
    {
        return $this->from(config('mail.from'))
            ->subject('Secret Phrase Reset Report')
            ->view('emails.secret_phrase_reset_report');
    }
}
