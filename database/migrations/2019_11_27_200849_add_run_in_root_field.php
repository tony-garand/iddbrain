<?php

use brain\Http\HypertargetingConfig;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRunInRootField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        HypertargetingConfig::addAllowedFieldToTemplate('gi-default', 'run_in_root');
        HypertargetingConfig::addAllowedFieldToTemplate('donate', 'run_in_root');
        HypertargetingConfig::addAllowedFieldToTemplate('donate-blackbaud', 'run_in_root');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        HypertargetingConfig::removeAllowedFieldFromTemplate('gi-default', 'run_in_root');
        HypertargetingConfig::removeAllowedFieldFromTemplate('donate', 'run_in_root');
        HypertargetingConfig::removeAllowedFieldFromTemplate('donate-blackbaud', 'run_in_root');
    }
}
