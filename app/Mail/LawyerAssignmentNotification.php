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

class LawyerAssignmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;
    public $lawyer;
    public $recipient_type;
    public $message;

    /**
     * Create a new message instance.
     */
    public function __construct(Complaint $complaint, User $lawyer, $recipient_type, $message = null)
    {
        $this->complaint = $complaint;
        $this->lawyer = $lawyer;
        $this->recipient_type = $recipient_type; // 'admin', 'complainant', 'respondent'
        $this->message = is_string($message) ? trim($message) : null;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->recipient_type) {
            'admin' => 'Lawyer Assigned to Case - ' . $this->complaint->case_number,
            'complainant' => 'Legal Review Initiated for Your Complaint - ' . $this->complaint->case_number,
            'respondent' => 'Legal Review Assigned - Case ' . $this->complaint->case_number,
            default => 'Legal Review Assignment - ' . $this->complaint->case_number,
        };

        return new Envelope(subject: $subject);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view = match($this->recipient_type) {
            'admin' => 'emails.lawyer-assigned-admin',
            'complainant' => 'emails.lawyer-assigned-complainant',
            'respondent' => 'emails.lawyer-assigned-respondent',
            default => 'emails.lawyer-assigned',
        };

        return new Content(
            view: $view,
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