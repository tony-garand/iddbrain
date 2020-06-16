<?php

use brain\Http\HypertargetingConfig;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixLabelingInHtConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rows = DB::table('hypertargeting')->orderBy('id')->get();
        foreach ($rows as $instance) {
            $config = json_decode($instance->config, true);
            if (!empty($config['blocks'][0]['block_title']['line1'])) {
                for ($i = 0; $i < env('HT_COUNT_BLOCK'); $i++) {
                    $config['blocks'][$i]['block_title']['line_1'] = $config['blocks'][$i]['block_title']['line1'];
                    unset($config['blocks'][$i]['block_title']['line1']);

                    $config['blocks'][$i]['block_title']['line_2'] = $config['blocks'][$i]['block_title']['line2'];
                    unset($config['blocks'][$i]['block_title']['line2']);

                    $config['blocks'][$i]['block_title']['line_3'] = $config['blocks'][$i]['block_title']['line3'];
                    unset($config['blocks'][$i]['block_title']['line3']);
                }
                HypertargetingConfig::update($instance->id, $config);
            }
        }
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
