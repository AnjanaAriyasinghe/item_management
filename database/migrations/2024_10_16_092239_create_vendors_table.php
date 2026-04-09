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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_code')->unique(); // Unique vendor code for identification
            $table->string('name'); // Vendor name
            $table->string('email')->nullable(); // Vendor email
            $table->string('phone')->nullable(); // Vendor phone number
            $table->string('mobile')->nullable();
            $table->longText('address')->nullable(); // Vendor address
            $table->string('remark')->nullable(); // small description regarding vendor
            $table->string('nic')->nullable()->unique();
            $table->string('br_no')->nullable()->unique();
            $table->enum('is_active',[0,1,2])->default(0)->comment('0-penging,1-active,2-deactive');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->foreign('deleted_by')->references('id')->on('users');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users');
            $table->dateTime('approved')->nullable();
            $table->string('approved_remark')->nullable()->unique();
            $table->string('rejected_remark')->nullable()->unique();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->foreign('rejected_by')->references('id')->on('users');
            $table->dateTime('rejected')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
