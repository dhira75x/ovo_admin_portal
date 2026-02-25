<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $fillable =[
        'claimant_id',
        'respondent_id',
        'reason',
        'status',
        'resolution_notes',
        'resolved_by',
        'resolved_at'
    ];

    protected $casts = [
        'resolved_at' => 'datetime'
    ];

    public function claimant()
    {
        return $this->belongsTo(User::class, 'claimant_id');
    }

    public function respondent()
    {
        return $this->belongsTo(User::class, 'respondent_id');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(Admin::class, 'resolved_by');
    }

    public function evidence()
    {
        return $this->hasMany(DisputeEvidence::class);
    }
}
