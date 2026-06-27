<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name', 50);
            $table->date('opening_date');
            $table->string('account_number', 50)->nullable();
            $table->string('currency', 3);
            $table->decimal('opening_balance', 28, 8);
            $table->text('description')->nullable();
            $table->bigInteger('business_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('accounts');
    }
};
