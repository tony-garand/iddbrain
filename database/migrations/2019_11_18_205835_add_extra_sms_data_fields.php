<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraSmsDataFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('sms_data', function (Blueprint $table) {
			$table->string('extra_1')->nullable();
			$table->string('extra_2')->nullable();
			$table->string('extra_3')->nullable();
			$table->string('extra_4')->nullable();
			$table->string('extra_5')->nullable();
			$table->string('extra_6')->nullable();
			$table->string('extra_7')->nullable();
			$table->string('extra_8')->nullable();
			$table->string('extra_9')->nullable();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('sms_data', function (Blueprint $table) {
			$table->removeColumn('extra_1');
			$table->removeColumn('extra_2');
			$table->removeColumn('extra_3');
			$table->removeColumn('extra_4');
			$table->removeColumn('extra_5');
			$table->removeColumn('extra_6');
			$table->removeColumn('extra_7');
			$table->removeColumn('extra_8');
			$table->removeColumn('extra_9');
		});
    }
}
