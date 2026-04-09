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
        Schema::create('cheque_book_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cheque_book_id'); // Foreign key to check_books
            $table->foreign('cheque_book_id')->references('id')->on('cheque_books');
            $table->unsignedBigInteger('signatory_id')->nullable(); // Foreign key to check_books
            $table->foreign('signatory_id')->references('id')->on('authorized_signatories');
            $table->integer('cheque_number'); // Number
            $table->decimal('amount', 15, 2)->nullable(); // Amount of the cheque
            $table->unsignedBigInteger('payment_id')->nullable(); // Link to payment if needed
            $table->date('cheque_date')->nullable();
            $table->date('issue_date')->nullable(); // Date the cheque was issued
            $table->date('clear_date')->nullable();
            $table->date('cancel_date')->nullable();
            $table->enum('status',['pending', 'issued', 'cleared', 'cancelled'])->default('pending');
            $table->enum('payee_name',['0','1'])->default('0')->comment('0-only_name|1-name_with_nic');
            $table->enum('payment_condition',['0','1','2'])->default('0')->comment('0-cash_payment|1-ac_payee|2-ac_payee-no-nego');
            $table->enum('validity_period',['0','1','2'])->default('0')->comment('0-none|1-30_days|60_days');
            $table->string('referance_no')->nullable();
            $table->string('cleared_comment')->nullable();
            $table->string('cancelled_comment')->nullable();
            $table->unsignedBigInteger('issued_by')->nullable(); // Foreign key to users
            $table->foreign('issued_by')->references('id')->on('users');
            $table->unsignedBigInteger('cleared_by')->nullable(); // Foreign key to users
            $table->foreign('cleared_by')->references('id')->on('users');
            $table->unsignedBigInteger('cancelled_by')->nullable(); // Foreign key to users
            $table->foreign('cancelled_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheque_book_details');
    }
};
