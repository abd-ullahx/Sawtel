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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            $table->bigInteger('customer_id')->unsigned();
            $table->string('subject');
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('priority')->default(0);
            $table->tinyInteger('is_resolved')->default(0);
            $table->bigInteger('operator_id')->unsigned()->nullable();
            $table->morphs('closed_user');
            $table->bigInteger('business_id')->unsigned();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
