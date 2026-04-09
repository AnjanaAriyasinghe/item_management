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
        Schema::table('cheque_book_details', function (Blueprint $table) {
            $table->boolean('payee')->default('0')->after('cancelled_by')->comment('0-cash_payee,1-account_payee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cheque_book_details', function (Blueprint $table) {
            $table->dropColumn('payee');
        });
    }
};
