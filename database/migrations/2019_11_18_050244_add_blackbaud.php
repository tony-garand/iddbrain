<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBlackbaud extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('hypertargeting', function (Blueprint $table) {
			$table->string('bbox_form_id')->nullable();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('hypertargeting', function (Blueprint $table) {
			$table->removeColumn('bbox_form_id');
		});
    }
}
