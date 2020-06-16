<form id="frm" class="form-horizontal add-hypertargeting-form" method="post" action="/hypertargeting/save" enctype="multipart/form-data">
{{ csrf_field() }}
<input type="hidden" name="blockcount" value="{{ $blockcount }}" />
<input type="hidden" name="factcount" value="{{ $factcount }}" />
<input type="hidden" name="testimonialcount" value="{{ $testimonialcount }}" />
<input type="hidden" name="aboutlogocount" value="{{ $aboutlogocount }}" />

	<h5 class="modal_h5">General Information</h5>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="hypertargeting_template_id" class="col-sm-3 control-label">Template:</label>
				<div class="col-sm-8">
					<select required class="form-control" id="hypertargeting_template_id" name="hypertargeting_template_id">
						<option value=""></option>
						@foreach($templates as $template)
							<option value="{{ $template->id }}">{{ $template->template_name }}</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="form-group">
				<label for="repo_name" class="col-sm-3 control-label">Repo Name:</label>
				<div class="col-sm-8">
					<input required type="text" class="clean form-control" id="repo_name" name="repo_name" placeholder="RepoName" />
				</div>
			</div>

			<div class="form-group">
				<label for="home_url" class="col-sm-3 control-label">Home Url:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="home_url" name="home_url" placeholder="http://example.com/" />
				</div>
			</div>

			<div class="form-group">
				<label for="pagetitle" class="col-sm-3 control-label">Page Title:</label>
				<div class="col-sm-8">
					<input required type="text" class="form-control" id="pagetitle" name="pagetitle" placeholder="My Page Title" />
				</div>
			</div>

			<div class="form-group">
				<label for="logo" class="col-sm-3 control-label">Logo:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.svg" id="logo" name="logo" type="file" />
				</div>
			</div>

		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="run_in_root" class="col-sm-3 control-label">Run in Webroot Directory?:</label>
				<div class="col-sm-8">
					<input type="checkbox" class="form-control" id="run_in_root" name="run_in_root" />
				</div>
			</div>

			<div class="form-group">
				<label for="name" class="col-sm-3 control-label">Company Name:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="name" name="name" placeholder="My Name" />
				</div>
			</div>

			<div class="form-group">
				<label for="phone" class="col-sm-3 control-label">Phone:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="phone" name="phone" placeholder="(123) 456-7890" />
				</div>
			</div>

			<div class="form-group">
				<label for="favicon" class="col-sm-3 control-label">Favicon:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.ico,.svg" id="favicon" name="favicon" type="file" />
				</div>
			</div>

		</div>
	</div>

	<div class="row">
		<div class="col-md-3">
			<div class="form-group">
				<label for="color1" class="col-sm-6 control-label">Color (Primary):</label>
				<div class="col-sm-6">
					<input type="text" class="form-control colorpick" id="color1" name="color1" placeholder="" value="#5653a0" />
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label for="color2" class="col-sm-6 control-label">Color (Second):</label>
				<div class="col-sm-6">
					<input type="text" class="form-control colorpick" id="color2" name="color2" placeholder="" value="#33889c" />
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label for="color3" class="col-sm-6 control-label">Color (Heart):</label>
				<div class="col-sm-6">
					<input type="text" class="form-control colorpick" id="color3" name="color3" placeholder="" value="#b13b4e" />
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label for="color4" class="col-sm-6 control-label">Color (Accent):</label>
				<div class="col-sm-6">
					<input type="text" class="form-control colorpick" id="color4" name="color4" placeholder="" value="#e0bb44" />
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<div class="col-sm-12">
					<label for="meta_description" class="control-label">Meta Description:</label>
					<textarea rows="2" class="form-control" id="meta_description" name="meta_description" placeholder=""></textarea>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<div class="col-sm-12">
					<label for="sources_list" class="control-label">Sources:</label>
					<textarea rows="2" class="form-control" id="sources_list" name="sources_list" placeholder="http://"></textarea>
				</div>
			</div>
		</div>
	</div>

	<h5 class="modal_h5">Hero Information</h5>
	<div class="row">
		<div class="col-md-6">

			<div class="form-group">
				<label for="hero_pretitle" class="col-sm-3 control-label">Hero Pre-Title:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="hero_pretitle" name="hero_pretitle" placeholder="" />
				</div>
			</div>

			<div class="form-group">
				<label for="hero_subtitle" class="col-sm-3 control-label">Hero Sub-Title:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="hero_subtitle" name="hero_subtitle" placeholder="" />
				</div>
			</div>

			<div class="form-group">
				<label for="hero_image" class="col-sm-3 control-label">Hero Image:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png" id="hero_image" name="hero_image" type="file" />
				</div>
			</div>

		</div>

		<div class="col-md-6">

			<div class="form-group">
				<label for="hero_title" class="col-sm-3 control-label">Hero Title:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="hero_title" name="hero_title" placeholder="" />
				</div>
			</div>

			<div class="form-group">
				<label for="hero_body" class="col-sm-3 control-label">Hero Body:</label>
				<div class="col-sm-8">
					<textarea rows="4" class="form-control" id="hero_body" name="hero_body" placeholder=""></textarea>
				</div>
			</div>

		</div>
	</div>


	<h5 class="modal_h5">Main Copy Section</h5>
	<div class="blocks">
		@for ($i = 1; $i <= $blockcount; $i++)
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="block_title_1_{{ $i }}" class="col-sm-3 control-label">Block Title 1:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="block_title_1_{{ $i }}" name="block_title_1_{{ $i }}" placeholder="" />
					</div>
				</div>
				<div class="form-group">
					<label for="block_title_2_{{ $i }}" class="col-sm-3 control-label">Block Title 2:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="block_title_2_{{ $i }}" name="block_title_2_{{ $i }}" placeholder="" />
					</div>
				</div>
				<div class="form-group">
					<label for="block_title_3_{{ $i }}" class="col-sm-3 control-label">Block Title 3:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="block_title_3_{{ $i }}" name="block_title_3_{{ $i }}" placeholder="" />
					</div>
				</div>

			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="block_image_{{ $i }}" class="col-sm-3 control-label">Block Image:</label>
					<div class="col-sm-8 fileupload_pad">
						<input accept=".gif,.jpg,.jpeg,.png" id="block_image_{{ $i }}" name="block_image_{{ $i }}" type="file" />
					</div>
				</div>
				<div class="form-group">
					<label for="block_body_{{ $i }}" class="col-sm-3 control-label">Block Body:</label>
					<div class="col-sm-8">
						<textarea rows="4" class="form-control" id="block_body_{{ $i }}" name="block_body_{{ $i }}" placeholder=""></textarea>
					</div>
				</div>
			</div>
		</div>
		@endfor
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="main_copy_heading" class="col-sm-3 control-label">Main Copy Heading:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="main_copy_heading" name="main_copy_heading" placeholder="" />
				</div>
			</div>
			<div class="form-group">
				<label for="main_copy_body" class="col-sm-3 control-label">Main Copy Body:</label>
				<div class="col-sm-8">
					<textarea rows="4" type="text" class="form-control" id="main_copy_body" name="main_copy_body" placeholder=""></textarea>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="main_copy_line_1" class="col-sm-3 control-label">Main Copy Bullet Item 1:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="main_copy_line_1" name="main_copy_line_1" placeholder="" />
				</div>
			</div>
			<div class="form-group">
				<label for="main_copy_line_2" class="col-sm-3 control-label">Main Copy Bullet Item 2:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="main_copy_line_2" name="main_copy_line_2" placeholder="" />
				</div>
			</div>
			<div class="form-group">
				<label for="main_copy_line_3" class="col-sm-3 control-label">Main Copy Bullet Item 3:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="main_copy_line_3" name="main_copy_line_3" placeholder="" />
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="main_copy_image" class="col-sm-3 control-label">Main Copy Image:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.svg" id="main_copy_image" name="main_copy_image" type="file" />
				</div>
			</div>
		</div>
	</div>

	<h5 class="modal_h5">Special Offer Section</h5>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="special_offer_title" class="col-sm-3 control-label">Special Offer Title:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="special_offer_title" name="special_offer_title" placeholder="" />
				</div>
			</div>
			<div class="form-group">
				<label for="special_offer_title_fine_print" class="col-sm-3 control-label">Special Offer Title Fine Print:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="special_offer_title_fine_print" name="special_offer_title_fine_print" placeholder="" />
				</div>
			</div>
			<div class="form-group">
				<label for="special_offer_image" class="col-sm-3 control-label">Special Offer Image:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.svg" id="special_offer_image" name="special_offer_image" type="file" />
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="special_offer_line_1" class="col-sm-3 control-label">Special Offer Line 1:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="special_offer_line_1" name="special_offer_line_1" placeholder="" />
				</div>
			</div>
			<div class="form-group">
				<label for="special_offer_fine_print" class="col-sm-3 control-label">Special Offer Fine Print:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="special_offer_fine_print" name="special_offer_fine_print" placeholder="" />
				</div>
			</div>
			<div class="form-group">
				<label for="special_offer_rebate_logo" class="col-sm-3 control-label">Special Offer Rebate Logo:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.svg" id="special_offer_rebate_logo" name="special_offer_rebate_logo" type="file" />
				</div>
			</div>
			<div class="form-group">
				<label for="special_offer_line_2" class="col-sm-3 control-label">Special Offer Line 2:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="special_offer_line_2" name="special_offer_line_2" placeholder="" />
				</div>
			</div>
			<div class="form-group">
				<label for="special_offer_line_3" class="col-sm-3 control-label">Special Offer Line 3:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="special_offer_line_3" name="special_offer_line_3" placeholder="" />
				</div>
			</div>
		</div>
	</div>


	<div class="facts">
		<h5 class="modal_h5">Fact / CTA Information</h5>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="banner_text" class="col-sm-3 control-label">CTA Banner:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="banner_text" name="banner_text" value="Don't wait any longer. Your screening colonoscopy could save your life!" />
					</div>
				</div>

				<div class="form-group">
					<label for="facts_title" class="col-sm-3 control-label">Facts Title:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="facts_title" name="facts_title" value="Know Your Colonoscopy Facts" />
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label for="facts_body" class="col-sm-3 control-label">Facts Desc:</label>
					<div class="col-sm-8">
						<textarea rows="4" class="form-control" id="facts_body" name="facts_body">Myths and fears about colonoscopies often get in the way of someone getting the life-saving screening they need. Learn the facts so you can be on your way to a pursuing a healthy, cancer-free life.</textarea>
					</div>
				</div>
			</div>
		</div>

		<h5 class="modal_h5">Fact Blocks</h5>
		@for ($i = 1; $i <= $factcount; $i++)
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="fact_title_{{ $i }}" class="col-sm-3 control-label">Fact Title:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="fact_title_{{ $i }}" name="fact_title_{{ $i }}" value="{{ $factdefaults[$i-1]['fact_title'] }}" />
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="fact_body_{{ $i }}" class="col-sm-3 control-label">Fact Body:</label>
					<div class="col-sm-8">
						<textarea rows="2" class="form-control" id="fact_body_{{ $i }}" name="fact_body_{{ $i }}" placeholder="">{{ $factdefaults[$i-1]['fact_body'] }}</textarea>
					</div>
				</div>
			</div>
		</div>
		@endfor
	</div>

	<div class="testimonials">
		<h5 class="modal_h5">Testimonial Information</h5>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="testimonials_title" class="col-sm-3 control-label">Testimonials Title:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="testimonials_title" name="testimonials_title" value="Stories From Our Patients" />
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label for="testimonials_body" class="col-sm-3 control-label">Testimonials Desc:</label>
					<div class="col-sm-8">
						<textarea rows="2" class="form-control" id="testimonials_body" name="testimonials_body"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label for="testimonials_background_image" class="col-sm-3 control-label">Testimonial Background Image:</label>
					<div class="col-sm-8 fileupload_pad">
						<input accept=".gif,.jpg,.jpeg,.png,.svg" id="testimonials_background_image"name="testimonials_background_image" type="file" />
					</div>
				</div>
			</div>
		</div>

		<h5 class="modal_h5">Testimonial Blocks</h5>
		@for ($i = 1; $i <= $testimonialcount; $i++)
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="testimonial_name_{{ $i }}" class="col-sm-3 control-label">Testimonial Name:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="testimonial_name_{{ $i }}" name="testimonial_name_{{ $i }}" placeholder="Bob Smith" />
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="testimonial_body_{{ $i }}" class="col-sm-3 control-label">Testimonial Body:</label>
					<div class="col-sm-8">
						<textarea rows="2" class="form-control" id="testimonial_body_{{ $i }}" name="testimonial_body_{{ $i }}"></textarea>
					</div>
				</div>
			</div>
		</div>
		@endfor
	</div>

	<h5 class="modal_h5">About Information</h5>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="about_title" class="col-sm-3 control-label">About Title:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="about_title" name="about_title" value="About Us" />
				</div>
			</div>

			<div class="form-group">
				<label for="about_link" class="col-sm-3 control-label">About Link:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="about_link" name="about_link" placeholder="http://" />
				</div>
			</div>

			<div class="form-group">
				<label for="about_image" class="col-sm-3 control-label">About Image:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.svg" id="about_image" name="about_image" type="file" />
				</div>
			</div>

			<div class="aboutLogos">
				@for ($i = 1; $i <= $aboutlogocount; $i++)
				<div class="form-group">
					<label for="about_logo_{{ $i }}" class="col-sm-3 control-label">About Logo:</label>
					<div class="col-sm-8 fileupload_pad">
						<input accept=".gif,.jpg,.jpeg,.png,.svg" id="about_logo_{{ $i }}" name="about_logo_{{ $i }}" type="file" />
					</div>
				</div>
				@endfor
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="about_body" class="col-sm-3 control-label">About Body:</label>
				<div class="col-sm-8">
					<textarea rows="5" class="form-control" id="about_body" name="about_body"></textarea>
				</div>
			</div>
			<div class="form-group">
				<label for="about_background_image" class="col-sm-3 control-label">About Background Image:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.svg" id="about_background_image" name="about_background_image" type="file" />
				</div>
			</div>
		</div>
	</div>

	<h5 class="modal_h5">Learn More Information</h5>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="learnmore_title" class="col-sm-3 control-label">More Title:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="learnmore_title" name="learnmore_title" value="Learn More" />
				</div>
			</div>

			<div class="form-group">
				<label for="learnmore_link" class="col-sm-3 control-label">More Link:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="learnmore_link" name="learnmore_link" placeholder="http://" />
				</div>
			</div>

			<div class="form-group">
				<label for="learnmore_image" class="col-sm-3 control-label">More Image:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.svg" id="learnmore_image" name="learnmore_image" type="file" />
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="learnmore_body" class="col-sm-3 control-label">More Body:</label>
				<div class="col-sm-8">
					<textarea rows="5" class="form-control" id="learnmore_body" name="learnmore_body">We want you to have all the information you need as you prepare for your colonoscopy. Learn more about the procedure here:</textarea>
				</div>
			</div>
		</div>
	</div>

	<h5 class="modal_h5">Miscellaneous Information</h5>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="form_url" class="col-sm-3 control-label">Form URL:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="form_url" name="form_url" placeholder="http://" />
				</div>
			</div>

			<div class="form-group">
				<label for="mobile_form_background_image" class="col-sm-3 control-label">Mobile Form Background Image:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.svg" id="mobile_form_background_image" name="mobile_form_background_image" type="file" />
				</div>
			</div>

			<div class="form-group">
				<label for="simplifi_submission_url" class="col-sm-3 control-label">Simplifi Submit URL:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="simplifi_submission_url" name="simplifi_submission_url" placeholder="http://" />
				</div>
			</div>

			<div class="form-group">
				<label for="recaptchakey" class="col-sm-3 control-label">Recaptcha Key:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control fillrandnum" id="recaptchakey" name="recaptcha_key" placeholder="XXXXXXXX" />
				</div>
			</div>

			<div class="form-group">
				<label for="recaptchasecretkey" class="col-sm-3 control-label">Recaptcha Secret:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control fillrandstring" id="recaptchasecretkey" name="recaptcha_secret_key" placeholder="XXXXXXXX" />
				</div>
			</div>

		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="simplifi_url" class="col-sm-3 control-label">Simplifi URL:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="simplifi_url" name="simplifi_url" placeholder="http://" />
				</div>
			</div>

			<div class="form-group">
				<label for="gakey" class="col-sm-3 control-label">GA Key:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control fillgakey" id="gakey" name="ga_key" placeholder="GA-XXXXXXX" />
				</div>
			</div>

			<div class="form-group">
				<label for="stripe_secret_key" class="col-sm-3 control-label">Stripe Secret:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control fillrandstring" id="stripe_secret_key" name="stripe_secret_key" placeholder="XXXXXXXX" />
				</div>
			</div>

			<div class="form-group">
				<label for="stripe_public_key" class="col-sm-3 control-label">Stripe Public:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control fillrandstring" id="stripe_public_key" name="stripe_public_key" placeholder="XXXXXXXX" />
				</div>
			</div>

			<div class="form-group">
				<label for="bbox_form_id" class="col-sm-3 control-label">Blackbaud ID:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control fillrandstring" id="bbox_form_id" name="bbox_form_id" placeholder="XXXXX-YYYY-ZZZZ" />
				</div>
			</div>

		</div>
	</div>

	<h5 class="modal_h5">More Information</h5>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<div class="col-sm-12">
					<label for="privacy_policy" class="control-label">Privacy Policy:</label>
					<textarea rows="3" class="form-control" id="privacy_policy" name="privacy_policy" placeholder=""></textarea>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<div class="col-sm-12">
					<label for="contact_block" class="control-label">Contact Block:</label>
					<textarea rows="3" class="form-control" id="contact_block" name="contact_block" placeholder=""></textarea>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<div class="col-sm-12">
					<label for="form_body_text" class="control-label">Text Above Donation Form:</label>
					<textarea rows="3" class="form-control" id="form_body_text" name="form_body_text" placeholder=""></textarea>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-9 fileupload_pad">
			<button id="add_btn" type="submit" class="btn btn-primary">Create</button>
		</div>
	</div>
</form>
