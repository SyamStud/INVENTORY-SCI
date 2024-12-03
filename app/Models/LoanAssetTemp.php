<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanAssetTemp extends Model
{
    protected $fillable = [
        'loan_temp_id',
        'asset_id',
        // 'quantity',
        'duration',
        'loan_check',
        'return_check',
        'notes',
        'branch_id',
    ];

    public function loan()
    {
        return $this->belongsTo(LoanTemp::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function branchOffice()
    {
        return $this->belongsTo(BranchOffice::class, 'branch_id');
    }
}
