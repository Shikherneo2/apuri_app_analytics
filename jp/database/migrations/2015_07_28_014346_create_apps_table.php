<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('apps', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->mediumText('package_name');
			$table->char('type',1);
			$table->mediumText('name');
			$table->mediumText('desc');
			$table->string('img_src',2083);
			$table->string('store_link',2083);
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
		Schema::drop('apps');
	}

}
