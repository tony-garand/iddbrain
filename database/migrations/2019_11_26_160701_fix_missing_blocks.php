<?php

use brain\Http\HypertargetingConfig;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixMissingBlocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $instances = DB::table('hypertargeting')->orderBy('id')->get();
        $instances->each(function ($instance) {
            $allowed_fields = HypertargetingConfig::getAllowedFieldsByInstance($instance->id);
            $config = json_decode($instance->config);
            if (in_array('testimonials', $allowed_fields) && empty($config->testminonials)) {
                for ($i = 0; $i < env('HT_COUNT_TESTIMONIAL'); $i++) {
                    $emptyTestimonial = [
                        'testimonial_name' => '',
                        'testimonial_body' => ''
                    ];
                    $config->testimonials[] = $emptyTestimonial;
                }
            }
            if (in_array('facts', $allowed_fields) && empty($config->facts)) {
                for ($i = 0; $i < env('HT_COUNT_FACT'); $i++) {
                    $emptyFact = [
                        'fact_title' => '',
                        'fact_body' => ''
                    ];
                    $config->facts[] = $emptyFact;
                }
            }
            $config = json_encode($config);
            DB::table('hypertargeting')->where('id', $instance->id)->update(['config' => $config]);
        });
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
