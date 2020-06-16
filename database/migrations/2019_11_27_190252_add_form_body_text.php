<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFormBodyText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $donate = DB::table('hypertargeting_templates')
            ->where('template_name', 'donate')
            ->first();
        $donateFields = unserialize($donate->allowed_fields);
        $donateFields[] = 'form_body_text';
        DB::table('hypertargeting_templates')
            ->where('template_name','donate')
            ->update([
                'allowed_fields' => serialize($donateFields)
            ]);

        $blackbaud = DB::table('hypertargeting_templates')
            ->where('template_name', 'donate-blackbaud')
            ->first();
        $blackbaudFields = unserialize($blackbaud->allowed_fields);
        $blackbaudFields[] = 'form_body_text';
        DB::table('hypertargeting_templates')
            ->where('template_name', 'donate-blackbaud')
            ->update([
                'allowed_fields' => serialize($blackbaudFields)
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $donate = DB::table('hypertargeting_templates')
            ->where('template_name', 'donate')
            ->first();
        $donateFields = unserialize($donate->allowed_fields);
        $donateFields = array_diff($donateFields, ['form_body_text']);
        DB::table('hypertargeting_templates')
            ->where('template_name','donate')
            ->update([
                'allowed_fields' => serialize($donateFields)
            ]);

        $blackbaud = DB::table('hypertargeting_templates')
            ->where('template_name', 'donate-blackbaud')
            ->first();
        $blackbaudFields = unserialize($blackbaud->allowed_fields);
        $blackbaudFields = array_diff($blackbaudFields, ['form_body_text']);
        DB::table('hypertargeting_templates')
            ->where('template_name','donate')
            ->update([
                'allowed_fields' => serialize($blackbaudFields)
            ]);
    }
}
