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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('expense_categories');
            $table->unsignedBigInteger('sub_category_id');
            $table->foreign('sub_category_id')->references('id')->on('expense_sub_categories');
            $table->unsignedBigInteger('vendor_id');
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->unsignedBigInteger('vendor_account_id')->nullable();
            $table->foreign('vendor_account_id')->references('id')->on('vender_has_accounts');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->decimal('balance', 15, 2);
            $table->date('expense_date');
            $table->date('due_date')->nullable();
            $table->enum('status',['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->enum('paymnet_status',['pending', 'partially', 'complete'])->default('pending');
            $table->string('pdf')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users');
            $table->date('approved_date')->nullable();
            $table->longText('approved_comment')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->foreign('rejected_by')->references('id')->on('users');
            $table->date('rejected_date')->nullable();
            $table->longText('rejected_comment')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->foreign('cancelled_by')->references('id')->on('users');
            $table->date('cancelled_date')->nullable();
            $table->longText('cancelled_comment')->nullable();
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
        Schema::dropIfExists('expenses');
    }
};
