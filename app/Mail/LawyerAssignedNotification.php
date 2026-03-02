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

class LawyerAssignedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;
    public $lawyer;
    public $message;

    /**
     * Create a new message instance.
     */
    public function __construct(Complaint $complaint, User $lawyer, $message = null)
    {
        $this->complaint = $complaint;
        $this->lawyer = $lawyer;
        $this->message = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Legal Review Assignment - ' . $this->complaint->case_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.lawyer-assigned',
            with: [
                'complaint' => $this->complaint,
                'lawyer' => $this->lawyer,
                'message' => $this->message,
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