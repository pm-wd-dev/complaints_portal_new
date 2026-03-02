<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'note',
        'next_steps',
        'created_by'
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
