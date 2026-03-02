<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if the user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if the user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Get complaints where user is a respondent
     */
    public function complaints()
    {
        return $this->hasMany(ComplaintRespondent::class, 'user_id');
    }

    /**
     * Get complaints where user is a lawyer
     */
    public function lawyerComplaints()
    {
        return $this->hasMany(ComplaintLawyer::class, 'user_id');
    }

    /**
     * Get all responses by this user through complaint_respondents
     */
    public function responses()
    {
        return $this->hasManyThrough(
            ComplaintResponse::class,
            ComplaintRespondent::class,
            'user_id', // Foreign key on complaint_respondents table
            'complaint_respondent_id', // Foreign key on complaint_responses table
            'id', // Local key on users table
            'id' // Local key on complaint_respondents table
        );
    }
}
