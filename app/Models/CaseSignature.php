<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseSignature extends Model
{
    protected $fillable = [
        'complaint_id',
        'resolution_id',
        'user_id',
        'signer_name',
        'signer_email',
        'signer_role',
        'signature_path',
        'signed_at',
        'ip_address'
    ];

    protected $casts = [
        'signed_at' => 'datetime'
    ];

    /**
     * Get the complaint this signature belongs to
     */
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    /**
     * Get the resolution this signature belongs to
     */
    public function resolution(): BelongsTo
    {
        return $this->belongsTo(CaseResolution::class, 'resolution_id');
    }

    /**
     * Get the user who signed (if not a guest)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this signature is from a guest
     */
    public function isGuestSignature(): bool
    {
        return is_null($this->user_id);
    }

    /**
     * Check if the signature is completed
     */
    public function isSigned(): bool
    {
        return !is_null($this->signed_at);
    }

    /**
     * Get the signature image URL
     */
    public function getSignatureUrl(): ?string
    {
        return $this->signature_path ? asset('storage/' . $this->signature_path) : null;
    }

    /**
     * Get formatted role name
     */
    public function getRoleName(): string
    {
        return ucfirst($this->signer_role);
    }

    /**
     * Get signer display name with role
     */
    public function getSignerDisplay(): string
    {
        return "{$this->signer_name} ({$this->getRoleName()})";
    }
}
