<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('salary_benefits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('salary_scale_id')->unsigned();
            $table->string('name', 191);
            $table->decimal('amount', 28, 8);
            $table->string('type', 20)->deffault('add')->comment('add | deduct');
            $table->bigInteger('business_id')->unsigned();
            $table->timestamps();

            $table->foreign('salary_scale_id')->references('id')->on('salary_scales')->cascadeOnDelete();
            $table->foreign('business_id')->references('id')->on('business')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('salary_benefits');
    }
};
