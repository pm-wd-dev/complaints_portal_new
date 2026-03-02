<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'step_number',
        'color',
        'is_active',
    ];



    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }


    public function scopeOrdered($query)
    {
        return $query->orderBy('step_number');
    }
}