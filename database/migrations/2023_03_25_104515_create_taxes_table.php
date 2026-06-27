<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->decimal('rate', 10, 2);
            $table->string('tax_number', 50)->nullable();
            $table->bigInteger('business_id')->unsigned();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('taxes');
    }
};
