<?php

namespace brain\Http;

use brain\Http\Controllers\HyperTargetingController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HypertargetingConfig {
	const STATUS_DEPLOYING = 0;
	const STATUS_DEPLOYED = 1;
	const STATUS_DELETED = 2;

    private static function hypertargetingEntry($id) {
        return DB::table('hypertargeting')->where('id', $id)->first();
    }

    private static function allowedFields($template_id) {
        $template = self::getTemplateEntry($template_id);
        if (empty($template)) {
            return null;
        }
        return unserialize($template->allowed_fields);
    }

    public static function getTemplateId($hypertargeting_id) {
        $entry = self::hypertargetingEntry($hypertargeting_id);
        if (empty($entry)) {
            return false;
        }
        return $entry->hypertargeting_template_id;
    }

    public static function getTemplateByInstance($hypertargeting_id) {
        $template_id = self::getTemplateId($hypertargeting_id);
        return self::getTemplateEntry($template_id);
    }

    private static function getTemplateEntry($template_id) {
        return DB::table('hypertargeting_templates')->where('id', $template_id)->first();
    }

    public static function getAllowedFieldsByInstance($id) {
        return self::allowedFields(self::getTemplateId($id));
    }

    public static function get($id) {
        $entry = self::hypertargetingEntry($id);
        if (empty($entry)) {
            return null;
        }
        $config = json_decode($entry->config);
        $config->id = $id;
        $config->hypertargeting_template_id = $entry->hypertargeting_template_id;
        return new HypertargetingInstance($config);
    }

    public static function getProperty($id, $name) {
        $instance = self::get($id);
        return $instance->$name;
    }

    private static function prepRepoName($name) {
        $name = strtolower(trim($name));
        return preg_replace('/(\s)/', '_', $name);
    }

    private static function mapBlocks($config) {
        for ($i = 1; $i <= env('HT_COUNT_BLOCK', 4); $i++) {
            if (!empty($config['block_image_' . $i])) {
                $config['blocks'][] = [
                    'block_image' => $config['block_image_' . $i],
                    'block_title' => [
                        'line_1' => $config['block_title_1_' . $i],
                        'line_2' => $config['block_title_2_' . $i],
                        'line_3' => $config['block_title_3_' . $i]
                    ],
                    'block_body' => $config['block_body_' . $i]
                ];
            }
        }
        return $config;
    }

    private static function mapFacts($config) {
        for ($i = 1; $i <= env('HT_COUNT_FACT', 7); $i++) {
            if (!empty($config['fact_title_' . $i])) {
                $config['facts'][] = [
                    'fact_title' => $config['fact_title_' . $i],
                    'fact_body' => $config['fact_body_' . $i]
                ];
            }
        }
        return $config;
    }

    private static function mapTestimonials($config) {
        for ($i = 1; $i <= env('HT_COUNT_TESTIMONIAL', 3); $i++) {
            if (!empty($config['testimonial_name_' . $i])) {
                $config['testimonials'][] = [
                    'testimonial_name' => $config['testimonial_name_' . $i],
                    'testimonial_body' => $config['testimonial_body_' . $i]
                ];
            }
        }
        return $config;
    }

    private static function mapAboutLogos($config) {
        for ($i = 1; $i <= env('HT_COUNT_ABOUT_LOGO', 5); $i++) {
            if (!empty($config['about_logo_' . $i])) {
                $config['about_logos'][] = $config['about_logo_' . $i];
            }
        }
        return $config;
    }

    private static function mapCheckboxes($config) {
        if (!empty($config['run_in_root'])) {
            $config['run_in_root'] = true;
        } else {
            $config['run_in_root'] = false;
        }
        return $config;
    }

    private static function mapSpecialFields($config) {
        $config = self::mapBlocks($config);
        $config = self::mapFacts($config);
        $config = self::mapTestimonials($config);
        $config = self::mapAboutLogos($config);
        $config = self::mapCheckboxes($config);
        return $config;
    }

    private static function prepConfig($config, $allowed) {
        if (is_object($config)) {
            $config = \object_to_array($config);
        }
        $config['repo_name'] = self::prepRepoName($config['repo_name']);
        $config = self::mapSpecialFields($config);
        $config = filter_array_by_keys($config, $allowed);
        return json_encode($config);
    }

    public static function update($id, $config) {
        $allowed = self::getAllowedFieldsByInstance($id);
        if (empty($allowed)) {
            return false;
        }
        $config = self::prepConfig($config, $allowed);
        DB::table('hypertargeting')
            ->where('id', $id)
            ->update([
                'config' => $config,
                'status' => self::STATUS_DEPLOYING,
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]);
    }

    public static function create($template_id, $config) {
        Log::info ('Template ID: ' . $template_id);
        $allowed = self::allowedFields($template_id);
        if (empty($allowed)) {
            return false;
        }
        Log::info('Allowed fields: ' . var_export($allowed, true));
        $preppedConfig = self::prepConfig($config, $allowed);

        return DB::table('hypertargeting')->insertGetId([
            'status' => self::STATUS_DEPLOYING,
            'hypertargeting_template_id' => $template_id,
            'config' => $preppedConfig,
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
        ]);
    }

    public static function modify($id, $property, $value) {
        $allowed = self::getAllowedFieldsByInstance($id);
        if (!in_array($property, $allowed)) {
            return false;
        }
        DB::table('hypertargeting')->where('id', $id)->update(["config->$property" => $value]);
    }

    public static function export($id) {
        $instance = DB::table('hypertargeting')->where('id', $id)->first();
        if (empty($instance)) {
            return null;
        }
        return $instance->config;   
    }

    public static function delete($id) {
        DB::table('hypertargeting')->where('id', $id)->delete();
    }

    public static function setStatus($id, $status) {
        DB::table('hypertargeting')->where('id', $id)->update(['status' => $status]);
    }

    public static function addAllowedFieldToTemplate($template_name, $field_name) {
        $template = DB::table('hypertargeting_templates')
            ->where('template_name', $template_name)
            ->first();
        if ($template) {
            $allowedFields = unserialize($template->allowed_fields);
            $allowedFields[] = $field_name;
            DB::table('hypertargeting_templates')
                ->where('template_name', $template_name)
                ->update([
                    'allowed_fields' => serialize($allowedFields)
                ]);
        } else {
            echo 'Failed to get template';
        }
    }

    public static function removeAllowedFieldFromTemplate($template_name, $field_name) {
        $template = DB::table('hypertargeting_templates')
            ->where('template_name', $template_name)
            ->first();
        if ($template) {
            $allowedFields = unserialize($template->allowed_fields);
            $allowedFields = array_diff($allowedFields, [$field_name]);
            DB::table('hypertargeting_templates')
                ->where('template_name', $template_name)
                ->update([
                    'allowed_fields' => serialize($allowedFields)
                ]);
        } else {
            echo 'Failed to get template';
        }
    }
 }