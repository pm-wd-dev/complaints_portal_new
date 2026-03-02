<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComplaintStatusUpdateNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;
    public $updateMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(Complaint $complaint, $updateMessage = null)
    {
        $this->complaint = $complaint;
        $this->updateMessage = $updateMessage ?: 'Your complaint has been assigned to a respondent for review.';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Complaint Update - ' . $this->complaint->case_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.complaint-status-update',
            with: [
                'complaint' => $this->complaint,
                'updateMessage' => $this->updateMessage,
                'trackingUrl' => route('public.complaints.view', ['caseNumber' => $this->complaint->case_number])
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
