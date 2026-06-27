<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->datetime('trans_date');
            $table->bigInteger('account_id')->unsigned()->nullable();
            $table->bigInteger('transaction_category_id')->unsigned()->nullable();
            $table->string('dr_cr', 2);
            $table->string('type', 20)->comment('income|expense|transfer|others');
            $table->decimal('amount', 28, 8);
            $table->decimal('currency_rate', 28, 8);
            $table->decimal('ref_amount', 28, 8)->nullable();
            $table->string('method', 100)->nullable();
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();
            $table->bigInteger('ref_id')->nullable();
            $table->string('ref_type')->nullable();
            $table->bigInteger('customer_id')->nullable(); // Income By Customer
            $table->bigInteger('vendor_id')->nullable(); // Expense By Vendor
            $table->bigInteger('business_id')->unsigned();
            $table->bigInteger('created_user_id')->nullable();
            $table->bigInteger('updated_user_id')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->cascadeOnDelete();
            $table->foreign('transaction_category_id')->references('id')->on('transaction_categories')->cascadeOnDelete();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('transactions');
    }
};
