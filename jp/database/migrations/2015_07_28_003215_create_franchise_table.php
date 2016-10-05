<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFranchiseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('franchise', function(Blueprint $table)
		{
			$table->bigInteger('id')->unsigned();
			$table->foreign('id')->references('id')->on('login')->onDelete('cascade');
			$table->mediumText('name');
			$table->string('email');
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
		Schema::drop('franchise');
	}

}
