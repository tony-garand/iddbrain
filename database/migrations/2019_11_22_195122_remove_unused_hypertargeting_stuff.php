<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUnusedHypertargetingStuff extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hypertargeting', function (Blueprint $table) {
            $columnsToDrop = ['repo_name', 'name', 'pagetitle', 'phone', 'color1', 'color2', 'color3',
                            'color4', 'logo', 'favicon', 'meta_description', 'form_url', 'simplifi_url',
                            'simplifi_submission_url', 'ga_key', 'recaptcha_key', 'hero_pretitle',
                            'hero_subtitle', 'hero_title', 'hero_image', 'hero_body', 'banner_text',
                            'facts_title', 'facts_body', 'testimonials_title', 'testimonials_body',
                            'about_title', 'about_image', 'about_link', 'about_body', 'learnmore_title',
                            'learnmore_image', 'learnmore_body', 'learnmore_link', 'sources_list',
                            'recaptcha_secret_key', 'stripe_secret_key', 'stripe_public_key',
                            'privacy_policy', 'contact_block', 'bbox_form_id'];
            $table->dropColumn($columnsToDrop);
        });

        Schema::dropIfExists('hypertargeting_blocks');
        Schema::dropIfExists('hypertargeting_facts');
        Schema::dropIfExists('hypertargeting_testimonials');
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
