<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 10)->comment('product || service');
            $table->bigInteger('product_unit_id')->nullable()->unsigned();
            $table->decimal('purchase_cost', 28, 8)->nullable();
            $table->decimal('selling_price', 28, 8)->nullable();
            $table->string('image')->nullable();
            $table->text('descriptions')->nullable();
            $table->tinyInteger('stock_management')->default(0);
            $table->decimal('stock', 10, 2)->nullable();
            $table->tinyInteger('allow_for_selling')->default(0);
            $table->tinyInteger('allow_for_purchasing')->default(0);
            $table->bigInteger('income_category_id')->unsigned()->nullable();
            $table->bigInteger('expense_category_id')->unsigned()->nullable();
            $table->tinyInteger('status')->default(1);
            $table->bigInteger('business_id')->unsigned();
            $table->bigInteger('created_user_id')->nullable();
            $table->bigInteger('updated_user_id')->nullable();
            $table->timestamps();

            $table->foreign('income_category_id')->references('id')->on('transaction_categories')->cascadeOnDelete();
            $table->foreign('expense_category_id')->references('id')->on('transaction_categories')->cascadeOnDelete();
            $table->foreign('product_unit_id')->references('id')->on('product_units')->nullOnDelete();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('products');
    }
};
