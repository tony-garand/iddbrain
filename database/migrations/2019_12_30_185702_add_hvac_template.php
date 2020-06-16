<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddHvacTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $allowed_fields = ['repo_name', 'name', 'pagetitle', 'phone', 'color1', 'color2',
                        'color3', 'color4', 'logo', 'favicon', 'meta_description', 'form_url',
                        'simplifi_url', 'simplifi_submission_url', 'ga_key', 'recaptcha_key',
                        'hero_title', 'hero_image', 'main_copy_heading', 'main_copy_body',
                        'main_copy_line_1', 'main_copy_line_2', 'main_copy_line_3', 'main_copy_image',
                        'special_offer_image', 'special_offer_title', 'special_offer_title_fine_print',
                        'special_offer_line_1', 'special_offer_line_2', 'special_offer_line_3',
                        'special_offer_fine_print', 'special_offer_rebate_logo', 'testimonials_title',
                        'about_title', 'about_body', 'about_background_image', 'about_logos', 
                        'testimonials', 'testimonials_background_image', 'mobile_form_background_image',
                        'run_in_root', 'contact_block'
                    ];
        DB::table('hypertargeting_templates')->insert([
            'template_name' => 'hvac',
            'repo_url' => 'git@bitbucket.org:id-digital/ht-hvac.git',
            'server_path' => '/var/www/gi.today/',
            'allowed_fields' => serialize($allowed_fields)
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('hypertargeting_templates')->where('template_name', '=', 'hvac')->delete();
    }
}
