<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisputeEvidence extends Model
{
    protected $table = 'dispute_evidences';

    protected $fillable = [
        'dispute_id',
        'uploader_id',
        'file_path',
        'description'
    ];

    public function dispute()
    {
        return $this->belongsTo(Dispute::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }
}
