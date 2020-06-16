<?php

use brain\Http\HypertargetingConfig;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetStatusToDeployed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('hypertargeting')
            ->where('status', HypertargetingConfig::STATUS_DEPLOYING)
            ->update(['status' => HypertargetingConfig::STATUS_DEPLOYED]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
