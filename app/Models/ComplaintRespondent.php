<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintRespondent extends Model
{
    protected $fillable = [
        'complaint_id',
        'user_id',
        'input',
        'responded_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all responses for this complaint respondent.
     */
    public function responses()
    {
        return $this->hasMany(ComplaintResponse::class)->orderBy('created_at', 'desc');
    }


    
}

