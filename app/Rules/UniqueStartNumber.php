<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueStartNumber implements Rule
{
    protected $bankAccountId;
    protected $ignoreId;

    public function __construct($bankAccountId, $ignoreId = null)
    {
        $this->bankAccountId = $bankAccountId;
        $this->ignoreId = $ignoreId;
    }

    public function passes($attribute, $value)
    {
        // Check in cheque_books table
        $chequeBooksQuery = DB::table('cheque_books')
            ->where('start_number', $value)
            ->where('bank_account_id', $this->bankAccountId);

        if ($this->ignoreId) {
            $chequeBooksQuery->where('id', '!=', $this->ignoreId);
        }

        $chequeBooksExists = $chequeBooksQuery->exists();

        // Check in cheque_book_details table
        $chequeBookDetailsExists = DB::table('cheque_book_details')
            ->join('cheque_books', 'cheque_book_details.cheque_book_id', '=', 'cheque_books.id')
            ->where('cheque_book_details.cheque_number', $value)
            ->where('cheque_books.bank_account_id', $this->bankAccountId)
            ->exists();


        return !$chequeBooksExists && !$chequeBookDetailsExists;
    }

    public function message()
    {
        return 'The start number has already been taken for the specified bank account.';
    }
}
