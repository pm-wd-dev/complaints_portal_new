<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class ComplaintSubmittedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;

    /**
     * Create a new message instance.
     */
    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Complaint Has Been Submitted - ' . $this->complaint->case_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.complaint-submitted',
            with: [
                'complaint' => $this->complaint,
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
        $attachments = [];
        
        // Load complaint attachments if not already loaded
        if (!$this->complaint->relationLoaded('attachments')) {
            $this->complaint->load('attachments');
        }
        
        foreach ($this->complaint->attachments as $attachment) {
            $filePath = public_path($attachment->file_path);
            if (file_exists($filePath)) {
                $attachments[] = Attachment::fromPath($filePath)
                    ->as(basename($attachment->file_path))
                    ->withMime('application/octet-stream');
            }
        }
        
        return $attachments;
    }
}
