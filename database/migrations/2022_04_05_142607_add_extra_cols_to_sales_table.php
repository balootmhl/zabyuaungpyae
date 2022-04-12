<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraColsToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->date('date')->nullable()->change();
            $table->integer('user_id')->nullable()->change();
            $table->integer('customer_id')->nullable()->change();
            $table->string('custom_name')->nullable();
            $table->text('custom_address')->nullable();
            $table->boolean('is_saleprice')->nullable();
            $table->string('invoice_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('custom_name');
            $table->dropColumn('custom_address');
            $table->dropColumn('is_saleprice');
            $table->dropColumn('invoice_code');
        });
    }
}
