<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LawyerResponseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'user_id',
        'lawyer_email',
        'case_number',
        'law_firm_name',
        'lawyer_city_state',
        'lawyer_name',
        'review_date',
        'legal_assessment',
        'legal_recommendations',
        'compliance_notes',
        'supporting_evidence_type',
        'evidence_description',
        'has_supporting_evidence',
        'submitted_at'
    ];

    protected $casts = [
        'review_date' => 'date',
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
        return $this->hasMany(Attachment::class, 'lawyer_response_id');
    }
}