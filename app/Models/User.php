<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Define relationships
     */

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function disputesAsClaimant()
    {
        return $this->hasMany(Dispute::class, 'claimant_id');
    }

    public function disputesAsRespondent()
    {
        return $this->hasMany(Dispute::class, 'respondent_id');
    }

    public function kycSubmissions()
    {
        return $this->hasMany(KycSubmission::class);
    }
}
