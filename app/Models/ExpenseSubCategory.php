<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseSubCategory extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'category_id',
        'name',
        'created_by',
        'updated_by',
        'references',
        'deleted_by',
    ];
    protected $table = 'expense_sub_categories';

    /**
     * Get the user that owns the ExpenseSubCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the user that owns the ExpenseSubCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id', 'id');
    }
}
