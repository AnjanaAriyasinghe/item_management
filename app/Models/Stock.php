<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stocks';

    protected $fillable = [
        'item_id',
        'transaction_type',
        'stock_quantity',
        'unit_price',
        'remark',
        'stock_date',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function created_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updated_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function deleted_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }
}
