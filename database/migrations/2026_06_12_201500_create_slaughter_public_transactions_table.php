<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slaughter_public_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('or_number', 50);
            $table->string('agency', 150);
            $table->string('payor', 150);
            $table->foreignId('cashier_user_id')->nullable();
            $table->timestamp('cashier_encoded_at')->nullable();
            $table->integer('small_heads')->default(0);
            $table->integer('goat_heads')->default(0);
            $table->integer('hog_heads')->default(0);
            $table->integer('large_heads')->default(0);
            $table->integer('cow_heads')->default(0);
            $table->integer('carabao_heads')->default(0);
            $table->decimal('small_kilos', 10, 2)->default(0);
            $table->decimal('large_kilos', 10, 2)->default(0);
            $table->decimal('pmf_amount', 10, 2)->default(0);
            $table->foreignId('slaughter_user_id')->nullable();
            $table->timestamp('slaughter_encoded_at')->nullable();
            $table->string('status', 30)->default('cashier_only');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slaughter_public_transactions');
    }
};
