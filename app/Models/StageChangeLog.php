<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StageChangeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'from_stage_id',
        'to_stage_id',
        'action',
        'description',
        'performed_by',
        'performer_role',
        'additional_data'
    ];

    protected $casts = [
        'additional_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function fromStage()
    {
        return $this->belongsTo(Stage::class, 'from_stage_id');
    }

    public function toStage()
    {
        return $this->belongsTo(Stage::class, 'to_stage_id');
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // Helper method to log a stage change
    public static function logChange($complaintId, $fromStageId, $toStageId, $action, $description = null, $additionalData = null)
    {
        // Don't skip logging for important actions even if stage doesn't change
        $importantActions = ['respondent_assigned', 'lawyer_assigned', 'complaint_resolved'];
        
        if ($fromStageId && $toStageId && $fromStageId == $toStageId && !in_array($action, $importantActions)) {
               return null;
        }
        return self::create([
            'complaint_id' => $complaintId,
            'from_stage_id' => $fromStageId,
            'to_stage_id' => $toStageId,
            'action' => $action,
            'description' => $description,
            'performed_by' => auth()->id(),
            'performer_role' => auth()->user() ? auth()->user()->role : 'system',
            'additional_data' => $additionalData
        ]);
    }
}
