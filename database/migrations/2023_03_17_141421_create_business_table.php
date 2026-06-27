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
        Schema::create('business', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('reg_no')->nullable();
            $table->string('vat_id')->nullable();
            $table->bigInteger('business_type_id')->unsigned()->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('country', 50);
            $table->string('currency', 10);
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('default')->default(0);
            $table->text('custom_fields')->nullable();
            $table->timestamps();

            $table->foreign('business_type_id')->references('id')->on('business_types')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business');
    }
};
