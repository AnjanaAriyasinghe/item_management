<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedBigInteger('expense_id');
            $table->foreign('expense_id')->references('id')->on('expenses');
            $table->unsignedBigInteger('vendor_id');
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->unsignedBigInteger('bank_account_id');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts');
            $table->unsignedBigInteger('cheque_book_id');
            $table->foreign('cheque_book_id')->references('id')->on('cheque_books');
            $table->unsignedBigInteger('cheque_book_detail_id');
            $table->foreign('cheque_book_detail_id')->references('id')->on('cheque_book_details');
            $table->string('cheque_number')->nullable();
            $table->decimal('amount', 15, 2);
            $table->date('payment_date')->nullable();
            $table->date('cheque_date')->nullable();
            $table->enum('status',['pending','issued','passed','reject'])->default('pending');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
