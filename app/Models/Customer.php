<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read \App\Models\User|null $createdby
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer withoutTrashed()
 * @mixin \Eloquent
 */
class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'customer_code',
        'name',
        'phone',
        'address',
        'city',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public static function boot(): void
    {
        parent::boot();
        static::creating(function ($customer) {
            $latest = Customer::withTrashed()->latest('id')->first();
            $nextId = $latest ? $latest->id + 1 : 1;
            $customer->customer_code = 'CUST-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        });
    }

    public function createdby(){
        return $this->belongsTo(User::class,'created_by');
    }
}
