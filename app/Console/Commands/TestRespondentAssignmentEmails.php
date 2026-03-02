<?php
namespace App\Console\Commands;

use App\Mail\ComplaintStatusUpdateNotification;
use App\Mail\RespondentAssignedNotification;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestRespondentAssignmentEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:respondent-assignment-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test respondent assignment email notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing respondent assignment email functionality...');

        // Get a test complaint
        $complaint = Complaint::latest()->first();

        if (! $complaint) {
            $this->error('No complaints found. Please create a complaint first.');
            return;
        }

        // Get a respondent user (cast member or admin)
        // $respondent = User::whereIn('role', ['cast_member', 'respondent'])->first();

        $respondent = User::where('email', 'shikha@yopmail.com')->first();

        if (! $respondent) {
            $this->error('No respondent users found. Please create a cast member or respondent user first.');
            return;
        }

        $this->info("Using complaint: {$complaint->case_number}");
        $this->info("Using respondent: {$respondent->name} ({$respondent->email})");

        try {
            // Test respondent assignment email
            $this->info("Sending assignment email to respondent: {$respondent->email}");
            Mail::to($respondent->email)->send(new RespondentAssignedNotification($complaint, $respondent));
            $this->info('✓ Respondent assignment email sent successfully');

            // Test complainant update email
            if ($complaint->email) {
                $this->info("Sending status update email to complainant: {$complaint->email}");
                $updateMessage = 'Your complaint has been assigned to a team member for review. Our team will investigate your concerns and respond accordingly.';
                Mail::to($complaint->email)->send(new ComplaintStatusUpdateNotification($complaint, $updateMessage));
                $this->info('✓ Complainant status update email sent successfully');
            } else {
                $this->warn('No email address found for complainant');
            }

            $this->info('Respondent assignment email test completed successfully!');
            $this->info("Respondent access URL (public): " . route('public.respondent.access', ['caseNumber' => $complaint->case_number]));
            $this->info("Respondent login URL: " . route('login.cast-member'));
            $this->info("Complainant tracking URL: " . route('public.complaints.view', ['caseNumber' => $complaint->case_number]));

        } catch (\Exception $e) {
            $this->error('Email test failed: ' . $e->getMessage());
        }
    }
}
