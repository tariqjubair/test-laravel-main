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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->integer('customer_id');
            $table->string('phone');
            $table->integer('sub_total');
            $table->integer('discount')->nullable();
            $table->string('discount_method')->nullable();
            $table->integer('discount_amount')->nullable();
            $table->integer('charge')->nullable();
            $table->integer('payment_method');
            $table->integer('total');
            $table->integer('notification_status')->default(0);
            $table->integer('status')->default(1);
            $table->date('delivery');
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
        Schema::dropIfExists('orders');
    }
};
