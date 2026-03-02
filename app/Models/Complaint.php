<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        // When deleting a complaint, also delete related records
        static::deleting(function($complaint) {
            // Delete all related records
            $complaint->respondents()->delete();
            $complaint->lawyers()->delete();
            $complaint->attachments()->delete();
            $complaint->investigationLogs()->delete();

            // Delete signatures and resolutions
            $complaint->resolutions->each(function($resolution) {
                $resolution->signatures()->delete();
                $resolution->delete();
            });
        });
    }

    protected $fillable = [
        'case_number',
        'submitted_as',
        'is_anonymous',
        'name',
        'email',
        'phone_number',
        'description',
        'location',
        'complaint_type',
        'complaint_about',
        'complainee_name',
        'complainee_email',
        'complainee_address',
        'witnesses',
        'evidence_type',
        'evidence_description',
        'status',
        'stage_id',
        'send_to',
        'lawyer_email',
        'lawyer_phone',
        'submitted_by_admin',
        'submitted_by_admin_id',
        'submitted_at',
        'date_of_experience',
        'attachment_path',
        'anonymity'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'date_of_experience' => 'datetime',
        'is_anonymous' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    // public function respondents()
    // {
    //     return $this->belongsToMany(User::class, 'complaint_respondents', 'complaint_id', 'user_id')
    //         ->withPivot('input', 'responded_at')
    //         ->withTimestamps();
    // }

    public function respondents()
{
    return $this->hasMany(ComplaintRespondent::class);
}

    public function lawyers()
    {
        return $this->hasMany(ComplaintLawyer::class);
    }

    public function adminUser()
    {
        return $this->belongsTo(User::class, 'submitted_by_admin_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    /**
     * Get all resolutions for this complaint
     */
    public function resolutions()
    {
        return $this->hasMany(CaseResolution::class);
    }

    /**
     * Get the latest resolution for this complaint
     */
    public function latestResolution()
    {
        return $this->hasOne(CaseResolution::class)->latest();
    }

    /**
     * Get all signatures for this complaint's latest resolution
     */
    public function signatures()
    {
        return $this->hasManyThrough(
            CaseSignature::class,
            CaseResolution::class,
            'complaint_id', // Foreign key on case_resolutions table
            'resolution_id', // Foreign key on case_signatures table
            'id', // Local key on complaints table
            'id' // Local key on case_resolutions table
        )->whereHas('resolution', function($query) {
            $query->where('case_resolutions.complaint_id', $this->id)
                  ->latest();
        });
    }

    public function investigationLogs()
    {
        return $this->hasMany(InvestigationLog::class);
    }

    /**
     * Get all responses for this complaint.
     */
    /**
     * Check if the complaint has pending signatures
     */
    public function hasPendingSignatures()
    {
        // Only check for pending signatures if the complaint is resolved
        if ($this->status !== 'resolved' || !$this->latestResolution) {
            \Log::info('Complaint is not resolved or has no latest resolution', [
                'complaint_id' => $this->id,
                'status' => $this->status,
                'has_latest_resolution' => $this->latestResolution ? 'yes' : 'no'
            ]);
            return false;
        }

        // Get the latest resolution's signatures
        $resolution = $this->latestResolution;
        $signatures = $resolution->signatures;

        $signerRoles = $signatures->pluck('signer_role');



        \Log::info('Signer roles', [
            'roles' => $signerRoles
        ]);

        // $hasComplainantSignature = false;
        // $hasRespondentSignature = false;
        // $hasLeadershipSignature = false;

        foreach($signerRoles as $role)
        {
                if ($role === 'complainant') {
                    $hasComplainantSignature = $signatures->contains(function ($signature) {
                        return $signature->user_id === null && $signature->signature_path !== null;
                    });

                    \Log::info('Complainant signature check', [
                        'has_signature' => $hasComplainantSignature,
                        'signatures_count' => $signatures->count()
                    ]);

                    if (!$hasComplainantSignature) {
                        return true; // Pending signature
                    }
                }

                if ($role === 'respondent') {
                    $respondent = $this->respondents->first();
                    $hasRespondentSignature = $respondent
                        ? $signatures->contains(function ($signature) use ($respondent) {
                            return $signature->user_id === $respondent->user_id && $signature->signature_path !== null;
                        })
                        : false;

                    \Log::info('Respondent signature check', [
                        'has_signature' => $hasRespondentSignature,
                        'signatures_count' => $signatures->count()
                    ]);

                    if (!$hasRespondentSignature) {
                        return true; // Pending signature
                    }
                }

                if ($role === 'leadership') {
                    $hasLeadershipSignature = $signatures->contains(function ($signature) {
                        return $signature->user && $signature->user->role === 'admin' && $signature->signature_path !== null;
                    });

                    \Log::info('Leadership signature check', [
                        'has_signature' => $hasLeadershipSignature,
                        'signatures_count' => $signatures->count()
                    ]);

                    if (!$hasLeadershipSignature) {
                        return true; // Pending signature
                    }
                }
            }

            return false;
    }


    /**
     * Get the display status for the complaint
     */
    public function getDisplayStatusAttribute()
    {
        if ($this->hasPendingSignatures()) {
            return 'awaiting_signature';
        }
        return $this->status;
    }

    public function responses()
    {
        return $this->hasManyThrough(
            ComplaintResponse::class,
            ComplaintRespondent::class,
            'complaint_id', // Foreign key on complaint_respondents table
            'complaint_respondent_id', // Foreign key on complaint_responses table
            'id', // Local key on complaints table
            'id' // Local key on complaint_respondents table
        );
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function stageChangeLogs()
    {
        return $this->hasMany(StageChangeLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get all respondent response details for this complaint
     */
    public function respondentResponseDetails()
    {
        return $this->hasMany(RespondentResponseDetail::class);
    }

    /**
     * Get all lawyer response details for this complaint
     */
    public function lawyerResponseDetails()
    {
        return $this->hasMany(LawyerResponseDetail::class);
    }

    public function replies()
    {
        return $this->hasMany(ComplaintReply::class);
    }
}
