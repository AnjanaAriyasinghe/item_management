<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'code',
        'expense_id',
        'vendor_id',
        'bank_account_id',
        'cheque_book_id',
        'cheque_book_detail_id',
        'cheque_number',
        'amount',
        'payment_date',
        'cheque_date',
        'status',
        'created_by',
    ];
    protected $table = 'payments';
    public static function boot()
    {
        parent::boot();
        static::creating(function ($payment) {
            $latestPayment= Payment::withTrashed()->latest('id')->first();
            if (  $latestPayment) {
                $latestPaymentId = $latestPayment->id + 1;
            } else {
                $latestPaymentId = 1;
            }
            $payment->code = 'PAY/VEN/' . str_pad($latestPaymentId, 3, '0', STR_PAD_LEFT);
        });
    }
    /**
     * Get the user that owns the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'id');
    }

    /**
     * Get the user that owns the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    /**
     * Get the user that owns the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bank_account(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id', 'id');
    }

    /**
     * Get the user that owns the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cheque_book(): BelongsTo
    {
        return $this->belongsTo(ChequeBook::class, 'cheque_book_id', 'id');
    }

    /**
     * Get the user that owns the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cheque_book_detail(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cheque_book_detail_id', 'id');
    }

    /**
     * Get the company of the expense associated with the payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): HasOneThrough
    {
        return $this->hasOneThrough(
            Company::class,    // The final model we want to access
            Expense::class,    // The intermediate model
            'id',              // Foreign key on the intermediate model (Expense)
            'id',              // Foreign key on the final model (Company)
            'expense_id',      // Local key on the starting model (Payment)
            'company_id'       // Local key on the intermediate model (Expense)
        );
    }
}
