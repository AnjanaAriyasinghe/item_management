<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales';

    protected $fillable = [
        'sale_no',
        'customer_id',
        'sale_date',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'total_amount',
        'note',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public static function boot(): void
    {
        parent::boot();
        static::creating(function ($sale) {
            $latest = Sale::withTrashed()->latest('id')->first();
            $nextId = $latest ? $latest->id + 1 : 1;
            $sale->sale_no = 'SALE-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
