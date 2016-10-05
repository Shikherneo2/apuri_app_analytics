<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopEmployeesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_employees', function(Blueprint $table)
		{
			$table->bigInteger('id')->unsigned();
			$table->primary('id');
			$table->foreign('id')->references('id')->on('login')->onDelete('cascade');
			$table->mediumText('name');
			$table->integer('shop_id')->unsigned();
			$table->foreign('shop_id')->references('id')->on('shops');
			$table->string('email');
			$table->softDeletes();
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
		Schema::drop('shop_employees');
	}

}
