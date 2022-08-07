<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameColToSaleitemsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('saleitems', function (Blueprint $table) {
			$table->string('name')->after('id')->nullable();
			$table->string('code')->after('id')->nullable();
			$table->dropColumn('is_sale_price');
		});

		Schema::table('purchaseitems', function (Blueprint $table) {
			$table->string('name')->after('id')->nullable();
			$table->string('code')->after('id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('saleitems', function (Blueprint $table) {
			$table->dropColumn('name');
			$table->dropColumn('code');
			$table->boolean('is_sale_price')->after('quantity')->nullable();
		});

		Schema::table('purchaseitems', function (Blueprint $table) {
			$table->dropColumn('name');
			$table->dropColumn('code');
		});
	}
}
