<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'account_name',
        'account_no',
        'bank_id',
        'branch_id',
        'ifsc_code',
        'created_by',
        'updated_by',
        'deleted_by',
        'company_id',
    ];
    protected $table = 'bank_accounts';

    /**
     * Get the user that owns the BankAccount
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the user that owns the BankAccount
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'id');
    }

    /**
     * Get the user that owns the BankAccount
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(BankBranch::class, 'branch_id', 'id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
