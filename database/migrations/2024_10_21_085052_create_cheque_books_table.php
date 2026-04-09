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
        Schema::create('cheque_books', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts');
            $table->string('book_code')->unique();
            $table->string('nikname')->nullable();
            $table->string('account_number'); // Associated account number
            $table->integer('number_of_cheque'); // Number of cheque count
            $table->integer('start_number'); // Starting cheque number
            $table->integer('end_number'); // Ending cheque number
            $table->enum('status', ['pending','approved','reject'])->default('pending');
            $table->string('approval_comment')->nullable();
            $table->date('approved_date')->nullable();
            $table->string('reject_comment')->nullable();
            $table->unsignedBigInteger('approved_user')->nullable();
            $table->foreign('approved_user')->references('id')->on('users');
            $table->unsignedBigInteger('reject_user')->nullable();
            $table->foreign('reject_user')->references('id')->on('users');
            $table->date('rejected_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->foreign('deleted_by')->references('id')->on('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheque_books');
    }
};
