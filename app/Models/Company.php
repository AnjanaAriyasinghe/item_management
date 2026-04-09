<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'system_title',
        'name',
        'description',
        'logo',
        'address',
        'contact_number',
        'mobile',
        'updated_by',
        'pv_no',
    ];
    protected $table = 'companies';

    /**
     * Get the user that owns the Company
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function users(){
        return $this->belongsToMany(User::class);
    }
    public function bank_accounts(){
        return $this->belongsToMany(BankAccount::class);
    }
}
