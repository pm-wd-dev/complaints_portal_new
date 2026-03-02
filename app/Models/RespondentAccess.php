<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespondentAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'complaint_id',
        'access_token',
        'access_type',
        'expires_at',
        'last_accessed_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function isValid()
    {
        return $this->expires_at > now();
    }

    public function updateLastAccess()
    {
        $this->update(['last_accessed_at' => now()]);
    }
}