<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

// デフォルトでのキュー投入
class AdminNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * 管理者から送られる件名と本文
     */
    public function __construct(
        public string $subjectLine,
        public string $bodyText
    )
    {}

    /**
     * メール送信者の設定
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Admin Notification Mail',
            from: new Address(
                env('MAIL_FROM_ADDRESS', 'hello@example.com'), 
                env('MAIL_FROM_NAME', 'Example'),
            ),
        );
    }

    /**
     * メール本文
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.AdminNotification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
