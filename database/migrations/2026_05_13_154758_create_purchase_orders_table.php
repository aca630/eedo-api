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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_order_id');
            $table->integer('cashier_id');
            $table->foreignId('product_id')
                ->constrained()
                ->restrictOnDelete();
            $table->integer('quantity');
            $table->decimal('cost_price', 12, 2);
            $table->decimal('retail_price', 12, 2);
            $table->integer('isvoided')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->index('purchase_order_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
};
