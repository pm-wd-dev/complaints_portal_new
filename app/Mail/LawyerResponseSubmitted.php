<?php

namespace App\Mail;

use App\Models\Complaint;
use App\Models\LawyerResponseDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LawyerResponseSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;
    public $response;

    /**
     * Create a new message instance.
     */
    public function __construct(Complaint $complaint, LawyerResponseDetail $response)
    {
        $this->complaint = $complaint;
        $this->response = $response;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Legal Review Completed - ' . $this->complaint->case_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.lawyer-response-submitted',
            with: [
                'complaint' => $this->complaint,
                'response' => $this->response,
                'complaintUrl' => route('admin.complaints.show', $this->complaint)
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
}
