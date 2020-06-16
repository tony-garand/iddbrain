<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientidSmsconvos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('sms_convos', function (Blueprint $table) {
			//
			$table->integer('client_id')->after('id')->default(0);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('sms_convos', function (Blueprint $table) {
			//
			$table->dropColumn('client_id');
		});
    }
}
