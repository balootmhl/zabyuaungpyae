<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBooleanPriceColToSaleitemsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('saleitems', function (Blueprint $table) {
			$table->boolean('is_sale_price')->nullable()->after('quantity');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('saleitems', function (Blueprint $table) {
			$table->dropColumn('is_sale_price');
		});
	}
}
