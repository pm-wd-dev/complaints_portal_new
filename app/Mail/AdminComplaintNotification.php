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

class AdminComplaintNotification extends Mailable
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
            subject: 'New Complaint Submitted - ' . $this->complaint->case_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-complaint-notification',
            with: [
                'complaint' => $this->complaint,
                'adminLoginUrl' => route('login'),
                'complaintUrl' => route('admin.complaints.show', $this->complaint->id)
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
