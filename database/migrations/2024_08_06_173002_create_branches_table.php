<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->after('email')->nullable();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->after('user_id')->nullable();
        });
        Schema::table('groups', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->after('user_id')->nullable();
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->after('user_id')->nullable();
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->after('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branches');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
}
