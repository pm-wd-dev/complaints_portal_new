<?php

namespace App\Console\Commands;

use App\Models\Complaint;
use App\Models\User;
use App\Mail\ComplaintSubmittedNotification;
use App\Mail\AdminComplaintNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestComplaintEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:complaint-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test complaint email notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing complaint email functionality...');

        // Get a test complaint (create one if none exists)
        $complaint = Complaint::latest()->first();

        if (!$complaint) {
            $this->error('No complaints found. Please create a complaint first.');
            return;
        }

        $this->info("Using complaint: {$complaint->case_number}");

        try {
            // Test complainant email
            if ($complaint->email) {
                $this->info("Sending test email to complainant: {$complaint->email}");
                Mail::to($complaint->email)->send(new ComplaintSubmittedNotification($complaint));
                $this->info('✓ Complainant email sent successfully');
            } else {
                $this->warn('No email address found for complainant');
            }

            // Test admin email
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                $this->info("Sending test email to admin: {$admin->email}");
                Mail::to($admin->email)->send(new AdminComplaintNotification($complaint));
                $this->info('✓ Admin email sent successfully');
            } else {
                $this->warn('No admin users found');
            }

            $this->info('Email test completed successfully!');
            $this->info("Direct complaint link: " . route('public.complaints.view', ['caseNumber' => $complaint->case_number]));

        } catch (\Exception $e) {
            $this->error('Email test failed: ' . $e->getMessage());
        }
    }
}
