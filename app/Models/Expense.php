<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'code',
        'company_id',
        'category_id',
        'sub_category_id',
        'description',
        'amount',
        'balance',
        'expense_date',
        'status',
        'paymnet_status',
        'pdf',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_date',
        'approved_comment',
        'rejected_by',
        'rejected_date',
        'rejected_comment',
        'deleted_by',
        'cancelled_by',
        'cancelled_date',
        'cancelled_comment',
        'vendor_id',
        'vendor_account_id',
        'due_date'
    ];
    protected $table = 'expenses';
    public static function boot()
    {
        parent::boot();
        static::creating(function ($expense) {
            $latestExpense = Expense::withTrashed()->latest('id')->first();
            if ($latestExpense) {
                $latestExpenseId = $latestExpense->id + 1;
            } else {
                $latestExpenseId = 1;
            }
            $expense->code = 'EXPENSE-' . str_pad($latestExpenseId, 3, '0', STR_PAD_LEFT);
        });
    }
    /**
     * Get all of the comments for the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    /**
     * Get the user that owns the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id', 'id');
    }

    /**
     * Get the user that owns the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sub_category(): BelongsTo
    {
        return $this->belongsTo(ExpenseSubCategory::class, 'sub_category_id', 'id');
    }

    /**
     * Get the user that owns the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the user that owns the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * Get the user that owns the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approved_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    /**
     * Get the user that owns the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rejected_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by', 'id');
    }
    /**
     * Get the user that owns the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    /**
     * Get the user that owns the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor_account(): BelongsTo
    {
        return $this->belongsTo(VenderHasAccount::class, 'vendor_account_id', 'id');
    }

    /**
     * Get the company that expense belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

}
