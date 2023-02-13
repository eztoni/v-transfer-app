<?php

namespace App\Mail\Partner;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyPartnerTransferOverviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Partner Transfer Overview',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.partner.daily-partner-transfer-overview',
        );
    }

    public function attachments(): array
    {


        $pdf = PDF::loadView('attachments.voucher', [
            'from'=>$this->reservation,
            'to'=>$this->reservation,
        ]);

        $this->attachData($pdf->output(),'Voucher.pdf');


        return [];
    }
}
