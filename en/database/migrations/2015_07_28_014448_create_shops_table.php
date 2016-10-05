<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shops', function(Blueprint $table)
		{
			$table->increments('id');
			$table->mediumText('name');
			$table->mediumText('location');
			$table->bigInteger('franchise_id')->unsigned();
			$table->foreign('franchise_id')->references('id')->on('franchise')->onDelete('cascade');
			$table->timestamps();
		});

		//Create trigger to delete the related employees when a shop is deleted
		DB::unprepared('
                CREATE TRIGGER delete_related_employees BEFORE DELETE ON `shops` FOR EACH ROW
                	BEGIN
                    	DELETE FROM login WHERE id=(SELECT id from shop_employees where shop_id=old.id);
                    	DELETE FROM login WHERE id=(SELECT id from shopkeeper where shop_id=old.id);
                    END
              ');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shops');
	}

}
