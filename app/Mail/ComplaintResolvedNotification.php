<?php

namespace App\Mail;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComplaintResolvedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;
    public $recipientType;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Complaint $complaint, string $recipientType, User $user = null)
    {
        $this->complaint = $complaint;
        $this->recipientType = $recipientType;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Complaint Resolved - Case #' . $this->complaint->case_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.complaint-resolved',
            with: [
                'complaint' => $this->complaint,
                'recipientType' => $this->recipientType,
                'user' => $this->user,
            ]
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