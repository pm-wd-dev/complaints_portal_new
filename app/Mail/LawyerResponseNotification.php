<?php

namespace App\Mail;

use App\Models\Complaint;
use App\Models\LawyerResponseDetail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LawyerResponseNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;
    public $response;
    public $recipient_type;

    /**
     * Create a new message instance.
     */
    public function __construct(Complaint $complaint, LawyerResponseDetail $response, $recipient_type)
    {
        $this->complaint = $complaint;
        $this->response = $response;
        $this->recipient_type = $recipient_type; // 'admin', 'complainant', 'respondent', 'lawyer'
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->recipient_type) {
            'admin' => 'Legal Review Completed - ' . $this->complaint->case_number,
            'complainant' => 'Legal Review Completed for Your Complaint - ' . $this->complaint->case_number,
            'respondent' => 'Legal Review Completed - Case ' . $this->complaint->case_number,
            'lawyer' => 'Confirmation: Legal Review Submitted - ' . $this->complaint->case_number,
            default => 'Legal Review Completed - ' . $this->complaint->case_number,
        };

        return new Envelope(subject: $subject);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view = match($this->recipient_type) {
            'admin' => 'emails.lawyer-response-admin',
            'complainant' => 'emails.lawyer-response-complainant',
            'respondent' => 'emails.lawyer-response-respondent',
            'lawyer' => 'emails.lawyer-response-lawyer',
            default => 'emails.lawyer-response-submitted',
        };

        return new Content(
            view: $view,
            with: [
                'complaint' => $this->complaint,
                'response' => $this->response,
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
        // Only admin gets attachments to avoid email size issues
        if ($this->recipient_type === 'admin') {
            $attachments = [];
            
            // Add lawyer response attachments to email if they exist
            if ($this->response->attachments && $this->response->attachments->count() > 0) {
                foreach ($this->response->attachments as $attachment) {
                    $filePath = public_path($attachment->file_path);
                    if (file_exists($filePath)) {
                        $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromPath($filePath)
                            ->as($attachment->file_path)
                            ->withMime('application/octet-stream');
                    }
                }
            }
            
            return $attachments;
        }
        
        return [];
    }
}