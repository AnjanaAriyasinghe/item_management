<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChequeBook extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'book_code',
        'bank_account_id',
        'nikname',
        'account_number',
        'number_of_cheque',
        'start_number',
        'end_number',
        'status',
        'approval_comment',
        'reject_comment',
        'approved_user',
        'reject_user',
        'created_by',
        'updated_by',
        'approved_date',
        'rejected_date',
        'deleted_by',
    ];
    protected $table = 'cheque_books';

    public static function boot()
    {
        parent::boot();
        static::creating(function ($chequeBook) {
            $latestChequeBook = ChequeBook::withTrashed()->latest('id')->first();
            if ($latestChequeBook) {
                $chequeBookCount = $latestChequeBook->id + 1;
            } else {
                $chequeBookCount = 1;
            }
            $chequeBook->book_code = 'CH-BOOK/' . str_pad($chequeBookCount, 3, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Get all of the comments for the ChequeBook
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cheques(): HasMany
    {
        return $this->hasMany(ChequeBookDetails::class, 'check_book_id', 'id');
    }

    /**
     * Get the user that owns the ChequeBook
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bank_account(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id', 'id');
    }

    /**
     * Get the user that owns the ChequeBook
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
    /**
     * Get the user that owns the ChequeBook
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approved_user_name(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_user', 'id');
    }

    /**
     * Get the user that owns the ChequeBook
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reject_user_name(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reject_user', 'id');
    }

    public function calculateEndNumber()
    {
        if (isset($this->start_number) && isset($this->number_of_cheque)) {
            $this->end_number = $this->start_number + $this->number_of_cheque - 1;
        }
    }

    public function save(array $options = [])
    {
        $this->calculateEndNumber();
        return parent::save($options);
    }
}
