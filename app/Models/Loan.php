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
        'status',
        'photos',
        'document_path',
        'branch_id',
        'user_id',
    ];

    public function scopePendingSignatures($query, $employeeId, $branchId)
    {
        return $query->where('status', 'pending')
            ->where('branch_id', $branchId)
            ->where(function ($query) use ($employeeId) {
                $query->where('operation_head', $employeeId)
                    ->orWhere('loan_officer', $employeeId)
                    ->orWhere('general_division', $employeeId);
            })
            ->whereDoesntHave('signatures', function ($query) use ($employeeId) {
                $query->where(function ($q) use ($employeeId) {
                    $q->where('position', 'KEPALA BIDANG OPERASI')
                        ->where('employee_id', $employeeId);
                })
                    ->orWhere(function ($q) use ($employeeId) {
                        $q->where('position', 'PETUGAS PINJAMAN')
                            ->where('employee_id', $employeeId);
                    })
                    ->orWhere(function ($q) use ($employeeId) {
                        $q->where('position', 'BAGIAN UMUM')
                            ->where('employee_id', $employeeId);
                    });
            });
    }

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

    public function assets()
    {
        return $this->hasMany(LoanAsset::class);
    }

    public function signatures()
    {
        return $this->morphMany(Signature::class, 'signable');
    }
}
