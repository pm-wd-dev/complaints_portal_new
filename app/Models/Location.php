<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'state',
        'address',
    ];

    public function qrCode()
    {
        return $this->hasOne(Attachment::class, 'location_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}
