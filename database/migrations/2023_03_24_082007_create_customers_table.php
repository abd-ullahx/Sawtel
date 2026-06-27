<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('business_id')->unsigned();
            $table->string('name', 50);
            $table->string('company_name', 100)->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('country')->nullable();
            $table->string('currency', 10);
            $table->string('vat_id')->nullable();
            $table->string('reg_no')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->text('address')->nullable();
            $table->text('remarks')->nullable();
            $table->string('profile_picture')->nullable();
            $table->decimal('balance', 28, 8)->default(0);
            $table->tinyInteger('login_status')->default(0);
            $table->text('custom_fields')->nullable();
            $table->bigInteger('created_user_id')->nullable();
            $table->bigInteger('updated_user_id')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('customers');
    }
};
