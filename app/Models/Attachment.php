<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'complaint_id',
        'complaint_response_id',
        'respondent_response_id',
        'lawyer_response_id',
        'uploaded_by',
        'file_path',
        'file_type',
        'description',
        'location_id',
        'type'
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function response()
    {
        return $this->belongsTo(ComplaintResponse::class, 'complaint_response_id');
    }

    public function respondentResponse()
    {
        return $this->belongsTo(RespondentResponseDetail::class, 'respondent_response_id');
    }

    public function lawyerResponse()
    {
        return $this->belongsTo(LawyerResponseDetail::class, 'lawyer_response_id');
    }
}
