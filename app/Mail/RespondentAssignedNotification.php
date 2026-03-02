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

class RespondentAssignedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;
    public $respondent;

    /**
     * Create a new message instance.
     */
    public function __construct(Complaint $complaint, User $respondent)
    {
        $this->complaint = $complaint;
        $this->respondent = $respondent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Complaint Assignment - ' . $this->complaint->case_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.respondent-assigned',
            with: [
                'complaint' => $this->complaint,
                'respondent' => $this->respondent,
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
