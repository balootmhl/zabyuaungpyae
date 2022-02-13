<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('user_id');
            $table->integer('supplier_id');
            $table->string('invoice_no')->nullable();
            $table->bigInteger('sub_total')->default(0)->nullable();
            $table->bigInteger('discount')->default(0)->nullable();
            $table->bigInteger('grand_total')->default(0)->nullable();
            $table->bigInteger('received')->default(0)->nullable();
            $table->bigInteger('remained')->default(0)->nullable();
            $table->timestamps();
        });
        Schema::create('purchaseitems', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_id');
            $table->integer('product_id');
            $table->integer('quantity');
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
        Schema::dropIfExists('purchaseitems');
        Schema::dropIfExists('purchases');
    }
}
