<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('user_id');
            $table->integer('customer_id');
            $table->string('invoice_no')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_cash')->default(1)->nullable();
            $table->bigInteger('sub_total')->default(0)->nullable();
            $table->bigInteger('discount')->default(0)->nullable();
            $table->bigInteger('grand_total')->default(0)->nullable();
            $table->bigInteger('received')->default(0)->nullable();
            $table->bigInteger('remained')->default(0)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
        Schema::create('saleitems', function (Blueprint $table) {
            $table->id();
            $table->integer('sale_id');
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
        Schema::dropIfExists('saleitems');
        Schema::dropIfExists('sales');
    }
}
