<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends Model
{
    use HasFactory;
    protected $fillable = [
        'bank_code',
        'bank_name',
    ];
    protected $table = 'banks';

    /**
     * Get all of the comments for the Bank
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(BankBranch::class, 'bank_id', 'id');
    }
}
