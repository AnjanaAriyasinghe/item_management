<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    protected $table = 'expense_categories';
    /**
     * Get the user that owns the ExpenseCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
    public function deleted_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }

}
