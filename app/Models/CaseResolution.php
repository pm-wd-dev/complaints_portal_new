<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseResolution extends Model
{
    protected $fillable = [
        'complaint_id',
        'resolution_text',
        'generated_pdf_path',
        'template_type',
        'generated_by'
    ];

    /**
     * Get the complaint this resolution belongs to
     */
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    /**
     * Get the admin who generated this resolution
     */
    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Get all signatures for this resolution
     */
    public function signatures(): HasMany
    {
        return $this->hasMany(CaseSignature::class, 'resolution_id');
    }

    /**
     * Check if all required signatures are completed
     */
    public function isFullySigned(): bool
    {
        return !$this->signatures()->whereNull('signed_at')->exists();
    }

    /**
     * Get the PDF file URL
     */
    public function getPdfUrl(): string
    {
        return asset($this->generated_pdf_path);
    }

    /**
     * Get pending signers
     */
    public function getPendingSigners()
    {
        return $this->signatures()->whereNull('signed_at')->get();
    }

    /**
     * Get completed signers
     */
    public function getCompletedSigners()
    {
        return $this->signatures()->whereNotNull('signed_at')->get();
    }
}
