<?php

use brain\Http\HypertargetingConfig;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixBlockTitleLabeling extends Migration
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
            if (!empty($config['blocks'][0]['block_title']['line1'])
                || (array_key_exists('line1', $config['blocks'][0]['block_title']) 
                    && $config['blocks'][0]['block_title']['line1'] === null)) {
                for ($i = 0; $i < count($config['blocks']); $i++) {
                    $config['blocks'][$i]['block_title']['line_1'] = $config['blocks'][$i]['block_title']['line1'];
                    unset($config['blocks'][$i]['block_title']['line1']);

                    $config['blocks'][$i]['block_title']['line_2'] = $config['blocks'][$i]['block_title']['line2'];
                    unset($config['blocks'][$i]['block_title']['line2']);

                    $config['blocks'][$i]['block_title']['line_3'] = $config['blocks'][$i]['block_title']['line3'];
                    unset($config['blocks'][$i]['block_title']['line3']);
                }
            }
            if (count($config['blocks']) < env('HT_COUNT_BLOCK')) {
                $difference = env('HT_COUNT_BLOCK') - count($config['blocks']);
                for ($i = 0; $i < $difference; $i++) {
                    $config['blocks'][] = [
                        'block_title' => [
                            'line_1' => '',
                            'line_2' => '',
                            'line_3' => ''
                        ],
                        'block_image' => '',
                        'block_body' => ''
                    ];
                }
            }
            DB::table('hypertargeting')->where('id', $instance->id)->update([
                'config' => json_encode($config),
                'status' => HypertargetingConfig::STATUS_DEPLOYED
            ]);
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
