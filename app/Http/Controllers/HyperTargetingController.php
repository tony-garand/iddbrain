<?php

namespace brain\Http\Controllers;

use brain\Http\HypertargetingConfig;
use brain\Jobs\Hypertargeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Image;
use Illuminate\Support\Facades\Storage;
use SSH;

class HyperTargetingController extends Controller {

	public function __construct() {
		$this->middleware('auth');
	}

	public function index() {

		$fact_defaults = [
			[
				'fact_title' => 'Colon cancer won\'t affect me.',
				'fact_body' => '<em>1 in 20 Americans will get colon cancer in their lifetime</em>, which is why it is so critical for men and women age 50-75 to get screened. If caught early, it\'s highly treatable and survivable.',
			],
			[
				'fact_title' => 'I\'m not having symptoms, so I don\'t need screening.',
				'fact_body' => 'Symptoms may be a sign that you already have advanced-stage cancer. Routine screening colonoscopies detect and remove pre-cancerous polyps, <em>so you can stop colon cancer before it starts.</em>',
			],
			[
				'fact_title' => 'I can\'t afford a colonoscopy.',
				'fact_body' => '<em>Your screening colonoscopy is fully covered by insurance under the Affordable Care Act (ACA)</em>. And if a polyp is found during your procedure, it can often be removed without additional costs or treatments.',
			],
			[
				'fact_title' => 'Colonoscopies hurt.',
				'fact_body' => 'Colonoscopies are done under sedation and last 30 to 60 minutes, so the procedure is pretty quick and painless. Most patients barely remember it happening and are back on their feet in no time.',
			],
			[
				'fact_title' => 'Colonoscopies are too risky.',
				'fact_body' => 'It\'s actually a very safe procedure. Serious complications are extremely rare <em>(about 2 in 1000 patients)</em>. You are far more likely to get colon cancer, which is very dangerous when left undetected.',
			],

			[
				'fact_title' => 'That prep stuff is gross.',
				'fact_body' => 'It\'s no mai tai on the beach, but it\'s not as bad as you think. With new flavors, dosages, and schedules, prep has gotten a lot better. A slightly unpleasant drink is worth it to <em>quickly detect and prevent cancer.</em>',
			],
			[
				'fact_title' => 'Other screening methods are fine.',
				'fact_body' => 'A colonoscopy examines the whole colon, providing a 95% detection rate for cancer and precancerous polyps. Other screening methods often only detect cancer once it\'s already formed.',
			],

		];

		$templates = DB::table('hypertargeting_templates')
					->select('hypertargeting_templates.*')
					->orderBy('template_name')
					->get();

		$hypertargeting = DB::table('hypertargeting')
					->join('hypertargeting_templates', 'hypertargeting.hypertargeting_template_id', '=', 'hypertargeting_templates.id')
					->select('hypertargeting.*', 'hypertargeting_templates.default_domain')
					->where('hypertargeting.status', '!=', HypertargetingConfig::STATUS_DELETED)
					->orderBy('id')
					->get();

		$hypertargeting->transform(function($item) {
			$config = json_decode($item->config, true);
			$item = (array) $item;
			return array_merge($item, $config);
		});
		
		$hypertargeting = $hypertargeting->sortBy('name');
		$hypertargeting->transform(function ($item) {
			return (object) $item;
		});

		return view('hypertargeting.index', [
			'templates' => $templates,
			'hypertargeting' => $hypertargeting,
			'blockcount' => env('HT_COUNT_BLOCK', 4),
			'factcount' => env('HT_COUNT_FACT', 7),
			'testimonialcount' => env('HT_COUNT_TESTIMONIAL', 3),
			'aboutlogocount' => env('HT_COUNT_ABOUT_LOGO', 5),
			'factdefaults' => $fact_defaults]);

	}

	public function view(Request $request, $id) {

		$templates = DB::table('hypertargeting_templates')
					->select('hypertargeting_templates.*')
					->orderBy('template_name')
					->get();
			
		$hypertargeting = HypertargetingConfig::get($id);

		return view('hypertargeting.view', [
			'templates' => $templates,
			'hypertargeting' => $hypertargeting,
			'blockcount' => env('HT_COUNT_BLOCK', 4),
			'factcount' => env('HT_COUNT_FACT', 7),
			'testimonialcount' => env('HT_COUNT_TESTIMONIAL', 3),
			'aboutlogocount' => env('HT_COUNT_ABOUT_LOGO', 5),
			'allowed_fields' => HypertargetingConfig::getAllowedFieldsByInstance($id)
		]);

	}

	public function delete(Request $request, $id) {
		$instance = HypertargetingConfig::get($id);
		$template = HypertargetingConfig::getTemplateByInstance($id);
		if ($instance && $template) {
			HypertargetingConfig::setStatus($id, HypertargetingConfig::STATUS_DELETED);
			
			$this->dispatch(new Hypertargeting($id, Hypertargeting::DEPLOY_TYPE_DELETE));

			$request->session()->flash("status", "HyperTargeting deleted successfully!");
			return redirect('/hypertargeting');
		} else {
			$request->session()->flash("error", "HyperTargeting could not be deleted!");
			return redirect('/hypertargeting');
		}

	}

	private static function validateRepoName(string $repoName, Request $request, string $redirectTo) {
		if (self::doesRepoNameExist($repoName)) {
			$request->session()->flash("error", "HyperTargeting client with that name already exists!");
			return redirect($redirectTo);
		}
	}

	private static function prepareConfig($initialConfig, $uploads, $repo_name) {
		$config = array_merge($initialConfig, $uploads);
		$config['repo_name'] = $repo_name;
		return $config;
	}

	public function update(Request $request, $id) {
		$repo_name = strtolower(trim($request->get('repo_name')));
		$instance = HypertargetingConfig::get($id);
		if ($repo_name != $instance->repo_name) {
			$request->session()->flash("error", "You can't change the repo name!");
			return redirect('/hypertargeting/view/'. $id);
		}

		$template = HypertargetingConfig::getTemplateByInstance($id);
		if ($instance && $template) {
				$uploads = self::updateImages($request, $instance);
				$config = self::prepareConfig($request->all(), $uploads, $instance->repo_name);

				HypertargetingConfig::update($id, $config);

				$this->dispatch(new Hypertargeting($id, Hypertargeting::DEPLOY_TYPE_UPDATE));
				$request->session()->flash("status", "HyperTargeting site updated successfully!");
				return redirect('/hypertargeting');
		} else {
			$request->session()->flash("error", "HyperTargeting could not be found!");
			return redirect('/hypertargeting');
		}
	}

	private static function updateImages($request, $instance) {
		$keysToCheck = ['logo', 'favicon', 'hero_image', 'about_image', 'learnmore_image',
					'main_copy_image', 'special_offer_image', 'special_offer_rebate_logo',
					'about_background_image', 'testimonials_background_image',
					'mobile_form_background_image'];

		$uploads = [];

		foreach ($keysToCheck as $key) {
			if (@$request->hasFile($key)) {
				self::deleteImage($instance->$key);
				$uploads[$key] = self::uploadImage($request->file($key), $instance->repo_name);
			} else {
				$uploads[$key] = $instance->$key;
			}
		}
		
		for ($i = 1; $i <= ($request->get('blockcount')+0); $i++) {
			if (@$request->hasFile('block_image_' . $i)) {
				self::deleteImage($instance->blocks[$i - 1]->block_image);
				$uploads['block_image_' . $i] = self::uploadImage($request->file('block_image_' . $i), $instance->repo_name);
			}
			elseif (!empty($instance->blocks)) {
				$uploads['block_image_' . $i] = $instance->blocks[$i - 1]->block_image;
			}
		}

		for ($i=1; $i <= ($request->get('aboutlogocount')+0); $i++) {
			if (@$request->hasFile('about_logo_' . $i)) {
				self::deleteImage($instance->about_logos[$i - 1]);
				$uploads['about_logo_' . $i] = self::uploadImage($request->file('about_logo_' . $i), $instance->repo_name);
			} elseif (!empty($instance->about_logos) && !empty($instance->about_logos[$i - 1])) {
				$uploads['about_logo_' . $i] = $instance->about_logos[$i - 1];
			}
		}
		
		return $uploads;
	}

	private static function uploadImages(Request $request, string $repo_name) {
		$keysToCheck = ['logo', 'favicon', 'hero_image', 'about_image', 'learnmore_image',
				'main_copy_image', 'special_offer_image', 'special_offer_rebate_logo',
				'about_background_image', 'testimonials_background_image',
				'mobile_form_background_image'];

		$uploads = [];

		foreach ($keysToCheck as $key) {
			if (@$request->hasFile($key)) {
				$uploads[$key] = self::uploadImage($request->file($key), $repo_name);
			}
		}

		for ($i=1; $i <= ($request->get('blockcount')+0); $i++) {
			if (@$request->hasFile('block_image_' . $i)) {
				$uploads['block_image_' . $i] = self::uploadImage($request->file('block_image_' . $i), $repo_name);
			}
		}

		for ($i=1; $i <= ($request->get('aboutlogocount')+0); $i++) {
			if (@$request->hasFile('about_logo_' . $i)) {
				$uploads['about_logo_' . $i] = self::uploadImage($request->file('about_logo_' . $i), $repo_name);
			}
		}

		return $uploads;
	}

 	public function save(Request $request) {
		$repo_name = strtolower(trim($request->get('repo_name')));
		self::validateRepoName($repo_name, $request, '/hypertargeting');

		$uploads = self::uploadImages($request, $repo_name);

		$config = self::prepareConfig($request->all(), $uploads, $repo_name);

		$ht_id = HypertargetingConfig::create($request->get('hypertargeting_template_id'), $config);

		if ($ht_id) {
			$this->dispatch(new Hypertargeting($ht_id, Hypertargeting::DEPLOY_TYPE_NEW));
			$request->session()->flash("status", "HyperTargeting site created successfully!");
			return redirect('/hypertargeting');
		}
	}

	public static function allowedFields($template_id) {
		$template = DB::table('hypertargeting_templates')->where('id', $template_id)->first();
		if (empty($template)) {
			return null;
		}
		return unserialize($template->allowed_fields);
	}

	private static function uploadImage($uploaded_file, $repo_name) {
		$save_name = "logo." . strtolower($uploaded_file->getClientOriginalExtension());
		$save_as = $repo_name . "/" . $save_name;

		if (strtolower($uploaded_file->getClientOriginalExtension()) == "svg") {
			Storage::disk('s3ht')->put($save_as, file_get_contents($uploaded_file), [
				'visibility' => 'public',
				'mimetype' => 'image/svg+xml'
			]);
		} else {
			$img = Image::make($uploaded_file)->orientate();
			$img->resize(1200, null, function ($constraint) {
				$constraint->aspectRatio();
				$constraint->upsize();
			});
			$img_big = $img->stream(null, 90);
			Storage::disk('s3ht')->put($save_as, $img_big->__toString(), 'public');
		}
		return Storage::disk('s3ht')->url($save_as);
	}

	private static function deleteImage($imageUrl) {
		$uri = parse_url($imageUrl);
		Storage::disk('s3ht')->delete($uri['path']);
	}

	private static function doesRepoNameExist($repo_name) {
		$ht_exists = DB::table('hypertargeting')->where('config->repo_name', $repo_name)->first();
		return (bool) $ht_exists;
	}

	private static function prepareDuplicateConfig($config, $new_repo_name) {
		$config->id = null;
		$config->name .= ' Clone';
		$config->repo_name = $new_repo_name;
		return $config;
	}

	public function duplicate(Request $request, $id, $new_repo_name) {
		$new_repo_name = strtolower(trim($new_repo_name));
		self::validateRepoName($new_repo_name, $request, '/hypertargeting/view/' . $id);

		$config = HypertargetingConfig::get($id);
		$config = self::prepareDuplicateConfig($config, $new_repo_name);

		$ht_id = HypertargetingConfig::create($config->hypertargeting_template_id, $config);
		if ($ht_id) {
			$this->dispatch(new Hypertargeting($ht_id, Hypertargeting::DEPLOY_TYPE_NEW));
			$request->session()->flash("status", "HyperTargeting site duplicated successfully!");
			return redirect('/hypertargeting/view/' . $ht_id);
		}
	}
}
