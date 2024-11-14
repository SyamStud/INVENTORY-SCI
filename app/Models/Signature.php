<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    protected $fillable = [
        'signable_type',
        'signable_id',
        'position',
        'signature_path',
        'is_signed',
        'signed_at'
    ];

    public function signable()
    {
        return $this->morphTo();
    }
}
