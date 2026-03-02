<?php

namespace App\Mail;

use App\Models\Complaint;
use App\Models\RespondentResponseDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RespondentResponseSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;
    public $response;
    public $isForAdmin;

    /**
     * Create a new message instance.
     */
    public function __construct(Complaint $complaint, RespondentResponseDetail $response, $isForAdmin = false)
    {
        $this->complaint = $complaint;
        $this->response = $response;
        $this->isForAdmin = $isForAdmin;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Respondent Response Received - ' . $this->complaint->case_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.respondent-response-submitted',
            with: [
                'complaint' => $this->complaint,
                'response' => $this->response,
                'isForAdmin' => $this->isForAdmin,
                'complaintUrl' => $this->isForAdmin 
                    ? route('admin.complaints.show', $this->complaint)
                    : route('public.complaints.view', $this->complaint->case_number)
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
        
        // Add respondent response attachments to email if they exist
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
