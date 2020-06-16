<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultDomain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('hypertargeting_templates', function (Blueprint $table) {
			$table->string('default_domain')->nullable();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('hypertargeting_templates', function (Blueprint $table) {
			$table->removeColumn('default_domain');
		});
    }
}
