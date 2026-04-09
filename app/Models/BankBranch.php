<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankBranch extends Model
{
    use HasFactory;
    protected $fillable = ['bank_id', 'bank_branch_code', 'bank_branch_name'];
    protected $table = 'bank_branches';

    /**
     * Get the user that owns the BankBranch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'id');
    }
}
