<?php

use brain\Http\HypertargetingConfig;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHomeUrlField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        HypertargetingConfig::addAllowedFieldToTemplate('gi-default', 'home_url');
        HypertargetingConfig::addAllowedFieldToTemplate('donate', 'home_url');
        HypertargetingConfig::addAllowedFieldToTemplate('donate-blackbaud', 'home_url');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        HypertargetingConfig::removeAllowedFieldFromTemplate('gi-default', 'home_url');
        HypertargetingConfig::removeAllowedFieldFromTemplate('donate', 'home_url');
        HypertargetingConfig::removeAllowedFieldFromTemplate('donate-blackbaud', 'home_url');
    }
}
