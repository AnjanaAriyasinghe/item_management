<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'items';

    protected $fillable = [
        'item_no',
        'item_code',
        'item_name',
        'item_description',
        'unit_price',
        'item_photo',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

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

    /**
     * All stock entries for this item.
     */
    public function stocks()
    {
        return $this->hasMany(\App\Models\Stock::class, 'item_id', 'id');
    }
}
