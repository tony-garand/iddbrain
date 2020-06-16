<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeHypertargetingStorageIntoJsonBlob extends Migration
{
    private function array_filter_by_keys($array, $allowed_keys) {
        return array_filter(
            $array,
            function ($key) use ($allowed_keys) {
                return in_array($key, $allowed_keys);
            },
            ARRAY_FILTER_USE_KEY
        );
    }
    
    private function map_array_with_key_filter($array, $allowed_keys) {
        return array_map(function ($item) use ($allowed_keys) {
            $item = json_decode(json_encode($item), true); //make sure item is an assoc array
            return $this->array_filter_by_keys($item, $allowed_keys);
        }, $array);
    }

    private function getBlocks($id) {
        $blocks = DB::table('hypertargeting_blocks')
                        ->where('hypertargeting_id', $id)
                        ->orderBy('id')
                        ->get();
        return array_map(function ($block) {
            return [
                'block_title' => [
                    'line1' => $block->block_title_1,
                    'line2' => $block->block_title_2,
                    'line3' => $block->block_title_3
                ],
                'block_image' => $block->block_image,
                'block_body' => $block->block_body
            ];
        }, $blocks->toArray());
    }

    private function getFacts($id) {
        $facts = DB::table('hypertargeting_facts')
            ->where('hypertargeting_id', $id)
            ->orderBy('id')
            ->get();
        $allowed = ['fact_title', 'fact_body'];
        return $this->map_array_with_key_filter($facts->toArray(), $allowed);
    }

    private function getTestimonials($id) {
        $testimonials = DB::table('hypertargeting_testimonials')
            ->where('hypertargeting_id', $id)
            ->orderBy('id')
            ->get();
        $allowed = ['testimonial_name', 'testimonial_body'];
        return $this->map_array_with_key_filter($testimonials->toArray(), $allowed);
    }

    private function doGiDefaultTemplate() {
        $template = DB::table('hypertargeting_templates')->where('template_name', 'gi-default')->first();
    
        $allowed_fields = unserialize($template->allowed_fields);

        $pages = DB::table('hypertargeting')->where('hypertargeting_template_id', $template->id)->orderBy('id')->get();
        $pages->each(function ($page) use ($allowed_fields) {
            $page->blocks = $this->getBlocks($page->id);
            $page->facts = $this->getFacts($page->id);
            $page->testimonials = $this->getTestimonials($page->id);
            $page_array = json_decode(json_encode($page), true); //convert to assoc array
            $page_array = $this->array_filter_by_keys($page_array, $allowed_fields);
            $config = json_encode($page_array);
            DB::table('hypertargeting')->where('id', $page->id)->update(['config' => $config]);
        });
    }

    private function convertTemplateIds() {
        DB::table('hypertargeting')->where('hypertargeting_template_id', 0)->update(['hypertargeting_template_id' => 1]);
    }

    private function unconvertTemplateIds() {
        DB::table('hypertargeting')->where('hypertargeting_template_id', 1)->update(['hypertargeting_template_id' => 0]);
    }

    private function doDonateTemplate() {
        $template = DB::table('hypertargeting_templates')->where('template_name', 'donate')->first();

        $allowed_fields = unserialize($template->allowed_fields);
        $pages = DB::table('hypertargeting')->where('hypertargeting_template_id', $template->id)->orderBy('id')->get();
        $pages->each(function ($page) use ($allowed_fields) {
            $page->blocks = $this->getBlocks($page->id);
            $page_array = json_decode(json_encode($page), true); //convert to assoc array
            $page_array = $this->array_filter_by_keys($page_array, $allowed_fields);
            $config = json_encode($page_array);
            DB::table('hypertargeting')->where('id', $page->id)->update(['config' => $config]);
        });
    }

    private function doDonateBlackbaudTemplate() {
        $template = DB::table('hypertargeting_templates')->where('template_name', 'donate-blackbaud')->first();

        $allowed_fields = unserialize($template->allowed_fields);
        $pages = DB::table('hypertargeting')->where('hypertargeting_template_id', $template->id)->orderBy('id')->get();
        $pages->each(function ($page) use ($allowed_fields) {
            $page->blocks = $this->getBlocks($page->id);
            $page_array = json_decode(json_encode($page), true); //convert to assoc array
            $page_array = $this->array_filter_by_keys($page_array, $allowed_fields);
            $config = json_encode($page_array);
            DB::table('hypertargeting')->where('id', $page->id)->update(['config' => $config]);
        });
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hypertargeting', function (Blueprint $table) {
            $table->mediumText('config');
        });

        $this->convertTemplateIds();
        $this->doGiDefaultTemplate();     
        $this->doDonateTemplate();   
        $this->doDonateBlackbaudTemplate();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hypertargeting', function (Blueprint $table) {
            $table->dropColumn('config');
        });

        $this->unconvertTemplateIds();
    }
}
