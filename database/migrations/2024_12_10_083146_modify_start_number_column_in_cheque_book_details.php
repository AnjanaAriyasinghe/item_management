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
                $table->string('cheque_number')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cheque_book_details', function (Blueprint $table) {
                $table->integer('cheque_number')->change();
        });
    }
};
