<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintResponse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'complaint_respondent_id',
        'response',
        'responded_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'responded_at' => 'datetime'
    ];

    /**
     * Get the complaint respondent that owns this response.
     */
    public function complaintRespondent()
    {
        return $this->belongsTo(ComplaintRespondent::class);
    }

    /**
     * Get the attachments for this response.
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'complaint_response_id');
    }
}
