<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespondentResponseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'user_id',
        'respondent_email',
        'case_number',
        'venue_legal_name',
        'venue_city_state',
        'respondent_name',
        'complaint_date',
        'respondent_side_story',
        'issue_detail_description',
        'witnesses_information',
        'supporting_evidence_type',
        'evidence_description',
        'has_supporting_evidence',
        'submitted_at'
    ];

    protected $casts = [
        'complaint_date' => 'date',
        'submitted_at' => 'datetime',
        'has_supporting_evidence' => 'boolean'
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'respondent_response_id');
    }

    public function getSupportingEvidenceTypeLabelAttribute()
    {
        return match($this->supporting_evidence_type) {
            'photos' => '📸 Photos/Screenshots',
            'videos' => '🎥 Videos',
            'messages' => '📧 Messages/Emails',
            'documents' => '📝 Other Documents',
            'none' => '❌ No supporting evidence',
            default => $this->supporting_evidence_type
        };
    }
}
