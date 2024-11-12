<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'loan_number',
        'customer_name',
        'operation_head',
        'loan_officer',
        'general_division',
        'branch_id',
        'user_id',
    ];

    public function operationHead()
    {
        return $this->belongsTo(Employee::class, 'operation_head');
    }

    public function loanOfficer()
    {
        return $this->belongsTo(Employee::class, 'loan_officer');
    }

    public function generalDivision()
    {
        return $this->belongsTo(Employee::class, 'general_division');
    }

    public function branchOffice()
    {
        return $this->belongsTo(BranchOffice::class, 'branch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
