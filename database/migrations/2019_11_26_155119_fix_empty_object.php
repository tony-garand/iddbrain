<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixEmptyObject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $results = DB::table('hypertargeting')->orderBy('id')->get();
        $results->each(function ($row) {
            $config = json_decode($row->config);
            if ($config->hero_image == new stdClass()) {
               DB::table('hypertargeting')->where('id', '=', $row->id)->update(['config->hero_image' => '']);
            }
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
