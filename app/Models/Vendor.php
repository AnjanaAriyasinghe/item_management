<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'vendor_code',
        'name',
        'email',
        'phone',
        'address',
        'remark',
        'nic',
        'created_by',
        'updated_by',
        'deleted_by',
        'mobile',
        'br_no',
        'approved_by',
        'approved',
        'rejected_by',
        'rejected',
        'approved_remark',
        'rejected_remark',
        'is_active'
    ];
    protected $table = 'vendors';
    public static function boot()
    {
        parent::boot();
        static::creating(function ($vendor) {
            $latestVendor = Vendor::withTrashed()->latest('id')->first();
            if ($latestVendor) {
                $latestVendorId = $latestVendor->id + 1;
            } else {
                $latestVendorId = 1;
            }
            $vendor->vendor_code = 'VENDOR-' . str_pad($latestVendorId, 3, '0', STR_PAD_LEFT);
        });
    }
    /**
     * Get all of the comments for the Vendor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(VenderHasAccount::class, 'vendor_id', 'id');
    }
    /**
     * Get the user that owns the Vendor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approved_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    /**
     * Get the user that owns the Vendor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rejected_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by', 'id');
    }
}
