<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnalyticsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('analytics', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('app_id')->unsigned();
			$table->foreign('app_id')->references('id')->on('apps');
			$table->bigInteger('emp_id')->unsigned()->nullable();
			$table->foreign('emp_id')->references('id')->on('shop_employees')->onDelete('cascade');
			$table->integer('counter');
			$table->char('device_id',64);
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
		Schema::drop('analytics');
	}

}
