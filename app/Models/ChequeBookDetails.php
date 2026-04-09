<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChequeBookDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'cheque_book_id',
        'cheque_number',
        'amount',
        'payment_id',
        'cheque_date',
        'issue_date',
        'clear_date',
        'cancel_date',
        'status',
        'issued_by',
        'cleared_by',
        'cancelled_by',
        'cleared_comment',
        'cancelled_comment',
        'payee',
        'signatory_id',
        'payee_name',
        'payment_condition',
        'validity_period',
        'referance_no',
    ];
    protected $table = 'cheque_book_details';

    /**
     * Get the user that owns the ChequeBookDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cheque_book(): BelongsTo
    {
        return $this->belongsTo(ChequeBook::class, 'cheque_book_id', 'id');
    }

    /**
     * Get the user that owns the ChequeBookDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function issued_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by', 'id');
    }

    /**
     * Get the user that owns the ChequeBookDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cleared_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cleared_by', 'id');
    }

    /**
     * Get the user that owns the ChequeBookDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cancelled_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by', 'id');
    }

    /**
     * Get the user that owns the ChequeBookDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }
}
