<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAllowedFieldsToHypertargetingTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hypertargeting_templates', function (Blueprint $table) {
            $table->mediumText('allowed_fields');
        });

        $giDefaultFeilds = ['name', 'pagetitle', 'phone', 'color1', 'color2', 'color3', 'color4',
                            'logo', 'favicon', 'meta_description', 'form_url', 'simplifi_url',
                            'simplifi_submission_url', 'ga_key', 'recaptcha_key', 'hero_pretitle',
                            'hero_subtitle', 'hero_title', 'hero_image', 'hero_body', 'banner_text',
                            'facts_title', 'facts_body', 'testimonials_title', 'testimonials_body',
                            'about_title', 'about_image', 'about_link', 'about_body', 
                            'learnmore_title','learnmore_image', 'learnmore_body', 'learnmore_link',
                            'sources_list', 'blocks', 'testimonials', 'facts', 'repo_name'];
        DB::table('hypertargeting_templates')
            ->where('template_name', 'gi-default')
            ->update(['allowed_fields' => serialize($giDefaultFeilds)]);

        $donateFields = ['name', 'pagetitle', 'phone', 'color1', 'color2', 'color3', 'color4',
                        'logo', 'favicon', 'meta_description', 'simplifi_url',
                        'simplifi_submission_url', 'ga_key', 'recaptcha_key', 'hero_pretitle',
                        'hero_subtitle', 'hero_title', 'hero_image', 'hero_body', 'banner_text',
                        'about_title', 'about_image', 'about_link', 'about_body', 
                        'learnmore_title','learnmore_image', 'learnmore_body', 'learnmore_link',
                        'sources_list', 'blocks', 'recaptcha_secret_key', 'stripe_secret_key',
                        'stripe_public_key', 'privacy_policy', 'contact_block', 'repo_name'];
        DB::table('hypertargeting_templates')
            ->where('template_name', 'donate')
            ->update(['allowed_fields' => serialize($donateFields)]);

        $donateBlackbaudFields = ['name', 'pagetitle', 'phone', 'color1', 'color2', 'color3', 'color4',
                        'logo', 'favicon', 'meta_description', 'simplifi_url',
                        'simplifi_submission_url', 'ga_key', 'hero_pretitle',
                        'hero_subtitle', 'hero_title', 'hero_image', 'hero_body', 'banner_text',
                        'about_title', 'about_image', 'about_link', 'about_body', 
                        'learnmore_title','learnmore_image', 'learnmore_body', 'learnmore_link',
                        'sources_list', 'blocks',
                        'privacy_policy', 'contact_block', 'bbox_form_id', 'repo_name'];
        DB::table('hypertargeting_templates')
            ->where('template_name', 'donate-blackbaud')
            ->update(['allowed_fields' => serialize($donateBlackbaudFields)]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hypertargeting_templates', function (Blueprint $table) {
            $table->dropColumn('allowed_fields');
        });
    }
}
