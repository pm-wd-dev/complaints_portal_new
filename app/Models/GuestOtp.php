<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GuestOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'phone',
        'otp',
        'expires_at',
        'is_verified',
        'verified_at',
        'ip_address'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_verified' => 'boolean'
    ];

    public function isExpired()
    {
        return $this->expires_at < Carbon::now();
    }

    public function isValid($otp)
    {
        return !$this->isExpired() && $this->otp === $otp && !$this->is_verified;
    }

    public function markAsVerified()
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => Carbon::now()
        ]);
    }

    public static function generateOtp($email, $phone = null, $ipAddress = null)
    {
        // Delete any existing OTPs for this email
        static::where('email', $email)->delete();

        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        return static::create([
            'email' => $email,
            'phone' => $phone,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
            'ip_address' => $ipAddress
        ]);
    }
}
