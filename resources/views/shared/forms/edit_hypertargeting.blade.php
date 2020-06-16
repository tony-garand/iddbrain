<form id="frm" class="form-horizontal" method="post" action="/hypertargeting/update/{{ $hypertargeting->id }}" enctype="multipart/form-data">
{{ csrf_field() }}
<input type="hidden" name="blockcount" value="{{ $blockcount }}" />
<input type="hidden" name="factcount" value="{{ $factcount }}" />
<input type="hidden" name="testimonialcount" value="{{ $testimonialcount }}" />
<input type="hidden" name="aboutlogocount" value="{{ $aboutlogocount }}" />
<input type="hidden" name="repo_name" value="{{ $hypertargeting->repo_name }}" />
<input type="hidden" name="hypertargeting_template_id" value="{{ $hypertargeting->hypertargeting_template_id }}" />

	<h5 class="modal_h5">General Information</h5>
	<div class="row">
		<div class="col-md-6">

			@if (in_array('pagetitle', $allowed_fields))
			<div class="form-group">
				<label for="pagetitle" class="col-sm-3 control-label">Page Title:</label>
				<div class="col-sm-8">
					<input required type="text" class="form-control" id="pagetitle" name="pagetitle" placeholder="My Page Title" value="{{ $hypertargeting->pagetitle }}" />
				</div>
			</div>
			@endif

			@if (in_array('home_url', $allowed_fields))
			<div class="form-group">
				<label for="home_url" class="col-sm-3 control-label">Home Url:</label>
				<div class="col-sm-8">
					<input required type="text" class="form-control" id="home_url" name="home_url" placeholder="http://example.com/" value="{{ $hypertargeting->home_url }}"/>
				</div>
			</div>
			@endif

			@if (in_array('logo', $allowed_fields))
			<div class="form-group">
				<label for="logo" class="col-sm-3 control-label">Logo:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.svg" id="logo" name="logo" type="file" />
					@if ($hypertargeting->logo)
						<div class="current_image">
							<a target="_new" href="{{ $hypertargeting->logo }}">current image</a>
						</div>
					@endif
				</div>
			</div>
			@endif

		</div>

		<div class="col-md-6">

			@if (in_array('run_in_root', $allowed_fields))
			<div class="form-group">
				<label for="run_in_root" class="col-sm-3 control-label">Run in Webroot Directory?:</label>
				<div class="col-sm-8">
					@if ($hypertargeting->run_in_root)
						<input type="checkbox" class="form-control" id="run_in_root" name="run_in_root" checked/>
					@else
						<input type="checkbox" class="form-control" id="run_in_root" name="run_in_root"/>
					@endif
				</div>
			</div>
			@endif

			@if (in_array('name', $allowed_fields))
			<div class="form-group">
				<label for="name" class="col-sm-3 control-label">Company Name:</label>
				<div class="col-sm-8">
					<input required type="text" class="form-control" id="name" name="name" placeholder="My Name" value="{{ $hypertargeting->name }}" />
				</div>
			</div>
			@endif

			@if (in_array('phone', $allowed_fields))
			<div class="form-group">
				<label for="phone" class="col-sm-3 control-label">Phone:</label>
				<div class="col-sm-8">
					<input required type="text" class="form-control" id="phone" name="phone" placeholder="(123) 456-7890" value="{{ $hypertargeting->phone }}" />
				</div>
			</div>
			@endif

			@if (in_array('favicon', $allowed_fields))
			<div class="form-group">
				<label for="favicon" class="col-sm-3 control-label">Favicon:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.ico,.svg" id="favicon" name="favicon" type="file" />
					@if ($hypertargeting->favicon)
						<div class="current_image">
							<a target="_new" href="{{ $hypertargeting->favicon }}">current image</a>
						</div>
					@endif
				</div>
			</div>
			@endif

		</div>
	</div>

	<div class="row">

		@if (in_array('color1', $allowed_fields))
		<div class="col-md-3">
			<div class="form-group">
				<label for="color1" class="col-sm-6 control-label">Color (Primary):</label>
				<div class="col-sm-6">
					<input required type="text" class="form-control colorpick" id="color1" name="color1" placeholder="" value="{{ $hypertargeting->color1 }}" />
				</div>
			</div>
		</div>
		@endif

		@if (in_array('color2', $allowed_fields))
		<div class="col-md-3">
			<div class="form-group">
				<label for="color2" class="col-sm-6 control-label">Color (Second):</label>
				<div class="col-sm-6">
					<input required type="text" class="form-control colorpick" id="color2" name="color2" placeholder="" value="{{ $hypertargeting->color2 }}" />
				</div>
			</div>
		</div>
		@endif

		@if (in_array('color3', $allowed_fields))
		<div class="col-md-3">
			<div class="form-group">
				<label for="color3" class="col-sm-6 control-label">Color (Heart):</label>
				<div class="col-sm-6">
					<input required type="text" class="form-control colorpick" id="color3" name="color3" placeholder="" value="{{ $hypertargeting->color3 }}" />
				</div>
			</div>
		</div>
		@endif

		@if (in_array('color4', $allowed_fields))
		<div class="col-md-3">
			<div class="form-group">
				<label for="color4" class="col-sm-6 control-label">Color (Accent):</label>
				<div class="col-sm-6">
					<input required type="text" class="form-control colorpick" id="color4" name="color4" placeholder="" value="{{ $hypertargeting->color4 }}" />
				</div>
			</div>
		</div>
		@endif

	</div>

	<div class="row">
		
		@if (in_array('meta_description', $allowed_fields))
		<div class="col-md-6">
			<div class="form-group">
				<div class="col-sm-12">
					<label for="meta_description" class="control-label">Meta Description:</label>
					<textarea required rows="2" class="form-control" id="meta_description" name="meta_description" placeholder="">{{ $hypertargeting->meta_description }}</textarea>
				</div>
			</div>
		</div>
		@endif

		@if (in_array('sources_list', $allowed_fields))
		<div class="col-md-6">
			<div class="form-group">
				<div class="col-sm-12">
					<label for="sources_list" class="control-label">Sources:</label>
					<textarea required rows="2" class="form-control" id="sources_list" name="sources_list" placeholder="http://">{{ $hypertargeting->sources_list }}</textarea>
				</div>
			</div>
		</div>
		@endif

	</div>

	<h5 class="modal_h5">Hero Information</h5>
	<div class="row">
		<div class="col-md-6">

			@if (in_array('hero_pretitle', $allowed_fields))
			<div class="form-group">
				<label for="hero_pretitle" class="col-sm-3 control-label">Hero Pre-Title:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="hero_pretitle" name="hero_pretitle" placeholder="" value="{{ $hypertargeting->hero_pretitle }}" />
				</div>
			</div>
			@endif

			@if (in_array('hero_subtitle', $allowed_fields))
			<div class="form-group">
				<label for="hero_subtitle" class="col-sm-3 control-label">Hero Sub-Title:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="hero_subtitle" name="hero_subtitle" placeholder="" value="{{ $hypertargeting->hero_subtitle }}" />
				</div>
			</div>
			@endif

			@if (in_array('hero_image', $allowed_fields))
			<div class="form-group">
				<label for="hero_image" class="col-sm-3 control-label">Hero Image:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png" id="hero_image" name="hero_image" type="file" />
					@if ($hypertargeting->hero_image)
						<div class="current_image">
							<a target="_new" href="{{ $hypertargeting->hero_image }}">current image</a>
						</div>
					@endif
				</div>
			</div>
			@endif

		</div>

		<div class="col-md-6">

			@if (in_array('hero_title', $allowed_fields))
			<div class="form-group">
				<label for="hero_title" class="col-sm-3 control-label">Hero Title:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="hero_title" name="hero_title" placeholder="" value="{{ $hypertargeting->hero_title }}" />
				</div>
			</div>
			@endif

			@if (in_array('hero_body', $allowed_fields))
			<div class="form-group">
				<label for="hero_body" class="col-sm-3 control-label">Hero Body:</label>
				<div class="col-sm-8">
					<textarea rows="4" class="form-control" id="hero_body" name="hero_body" placeholder="">{{ $hypertargeting->hero_body }}</textarea>
				</div>
			</div>
			@endif

		</div>
	</div>


	<h5 class="modal_h5">Main Copy Section</h5>
	@if (count(in_array('blocks', $allowed_fields)) > 0)
		@for ($i = 1; $i <= $blockcount; $i++)
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="block_title_1_{{ $i }}" class="col-sm-3 control-label">Block Title 1:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="block_title_1_{{ $i }}" name="block_title_1_{{ $i }}" value="{{ $hypertargeting->blocks[$i - 1]->block_title->line_1 }}" />
					</div>
				</div>
				<div class="form-group">
					<label for="block_title_2_{{ $i }}" class="col-sm-3 control-label">Block Title 2:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="block_title_2_{{ $i }}" name="block_title_2_{{ $i }}" value="{{ $hypertargeting->blocks[$i - 1]->block_title->line_2 }}" />
					</div>
				</div>
				<div class="form-group">
					<label for="block_title_3_{{ $i }}" class="col-sm-3 control-label">Block Title 3:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="block_title_3_{{ $i }}" name="block_title_3_{{ $i }}" value="{{ $hypertargeting->blocks[$i - 1]->block_title->line_3 }}" />
					</div>
				</div>

			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="block_image_{{ $i }}" class="col-sm-3 control-label">Block Image:</label>
					<div class="col-sm-8 fileupload_pad">
						<input accept=".gif,.jpg,.jpeg,.png" id="block_image_{{ $i }}" name="block_image_{{ $i }}" type="file" />
						@if ($hypertargeting->blocks[$i - 1]->block_image)
							<div class="current_image">
								<a target="_new" href="{{ $hypertargeting->blocks[$i - 1]->block_image }}">current image</a>
							</div>
						@endif
					</div>
				</div>
				<div class="form-group">
					<label for="block_body_{{ $i }}" class="col-sm-3 control-label">Block Body:</label>
					<div class="col-sm-8">
						<textarea rows="4" class="form-control" id="block_body_{{ $i }}" name="block_body_{{ $i }}">{{ $hypertargeting->blocks[$i - 1]->block_body }}</textarea>
					</div>
				</div>
			</div>
		</div>
		@endfor
	@endif
	<div class="row">
		<div class="col-md-6">
			@if (in_array('main_copy_heading', $allowed_fields))
				<div class="form-group">
					<label for="main_copy_heading" class="col-sm-3 control-label">Main Copy Heading:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="main_copy_heading" name="main_copy_heading" value="{{ $hypertargeting->main_copy_heading }}" />
					</div>
				</div>
			@endif

			@if (in_array('main_copy_body', $allowed_fields))
				<div class="form-group">
					<label for="main_copy_body" class="col-sm-3 control-label">Main Copy Body:</label>
					<div class="col-sm-8">
						<textarea rows="4" type="text" class="form-control" id="main_copy_body" name="main_copy_body" value="{{ $hypertargeting->main_copy_body }}"></textarea>
					</div>
				</div>
			@endif
		</div>
		<div class="col-md-6">
			@if (in_array('main_copy_line_1', $allowed_fields))
				<div class="form-group">
					<label for="main_copy_line_1" class="col-sm-3 control-label">Main Copy Bullet Item 1:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="main_copy_line_1" name="main_copy_line_1" value="{{ $hypertargeting->main_copy_line_1 }}" />
					</div>
				</div>
			@endif

			@if (in_array('main_copy_line_2', $allowed_fields))
				<div class="form-group">
					<label for="main_copy_line_2" class="col-sm-3 control-label">Main Copy Bullet Item 2:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="main_copy_line_2" name="main_copy_line_2" value="{{ $hypertargeting->main_copy_line_3 }}" />
					</div>
				</div>
			@endif

			@if (in_array('main_copy_line_3', $allowed_fields))
				<div class="form-group">
					<label for="main_copy_line_3" class="col-sm-3 control-label">Main Copy Bullet Item 3:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="main_copy_line_3" name="main_copy_line_3" value="{{ $hypertargeting->main_copy_line_3 }}" />
					</div>
				</div>
			@endif
		</div>

		<div class="col-md-6">
			@if (in_array('main_copy_image', $allowed_fields))
				<div class="form-group">
					<label for="main_copy_image" class="col-sm-3 control-label">Main Copy Image:</label>
					<div class="col-sm-8 fileupload_pad">
						<input accept=".gif,.jpg,.jpeg,.png,.svg" id="main_copy_image" name="main_copy_image" type="file" />
						@if ($hypertargeting->main_copy_image)
							<div class="current_image">
								<a target="_new" href="{{ $hypertargeting->main_copy_image }}">current image</a>
							</div>
						@endif
					</div>
				</div>
			@endif
		</div>
	</div>

	<h5 class="modal_h5">Special Offer Section</h5>
	<div class="row">
		<div class="col-md-6">
			@if (in_array('special_offer_title', $allowed_fields))
			<div class="form-group">
				<label for="special_offer_title" class="col-sm-3 control-label">Special Offer Title:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="special_offer_title" name="special_offer_title" value="{{ $hypertargeting->special_offer_title }}" />
				</div>
			</div>
			@endif

			@if (in_array('special_offer_title_fine_print', $allowed_fields))
			<div class="form-group">
				<label for="special_offer_title_fine_print" class="col-sm-3 control-label">Special Offer Title Fine Print:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="special_offer_title_fine_print" name="special_offer_title_fine_print" value="{{ $hypertargeting->special_offer_title_fine_print }}" />
				</div>
			</div>
			@endif

			@if (in_array('special_offer_image', $allowed_fields))
			<div class="form-group">
				<label for="special_offer_image" class="col-sm-3 control-label">Special Offer Image:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.svg" id="special_offer_image" name="special_offer_image" type="file" />
					@if ($hypertargeting->special_offer_image)
						<div class="current_image">
							<a target="_new" href="{{ $hypertargeting->special_offer_image }}">current image</a>
						</div>
					@endif
				</div>
			</div>
			@endif

		</div>
		
		<div class="col-md-6">
			@if (in_array('special_offer_line_1', $allowed_fields))
			<div class="form-group">
				<label for="special_offer_line_1" class="col-sm-3 control-label">Special Offer Line 1:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="special_offer_line_1" name="special_offer_line_1" value="{{ $hypertargeting->special_offer_line_1 }}" />
				</div>
			</div>
			@endif

			@if (in_array('special_offer_fine_print', $allowed_fields))
			<div class="form-group">
				<label for="special_offer_fine_print" class="col-sm-3 control-label">Special Offer Fine Print:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="special_offer_fine_print" name="special_offer_fine_print" value="{{ $hypertargeting->special_offer_fine_print }}" />
				</div>
			</div>
			@endif

			@if (in_array('special_offer_rebate_logo', $allowed_fields))
			<div class="form-group">
				<label for="special_offer_rebate_logo" class="col-sm-3 control-label">Special Offer Rebate Logo:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.svg" id="special_offer_rebate_logo" name="special_offer_rebate_logo" type="file" />
					@if ($hypertargeting->special_offer_rebate_logo)
						<div class="current_image">
							<a target="_new" href="{{ $hypertargeting->special_offer_rebate_logo }}">current image</a>
						</div>
					@endif
				</div>
			</div>
			@endif
			
			@if (in_array('special_offer_line_2', $allowed_fields))
			<div class="form-group">
				<label for="special_offer_line_2" class="col-sm-3 control-label">Special Offer Line 2:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="special_offer_line_2" name="special_offer_line_2" value="{{ $hypertargeting->special_offer_line_2 }}" />
				</div>
			</div>
			@endif

			@if (in_array('special_offer_line_3', $allowed_fields))
			<div class="form-group">
				<label for="special_offer_line_3" class="col-sm-3 control-label">Special Offer Line 3:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="special_offer_line_3" name="special_offer_line_3" value="{{ $hypertargeting->special_offer_line_3 }}" />
				</div>
			</div>
			@endif
		</div>
	</div>

	@if (in_array('facts', $allowed_fields))
		<h5 class="modal_h5">Fact / CTA Information</h5>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="banner_text" class="col-sm-3 control-label">CTA Banner:</label>
					<div class="col-sm-8">
						<input required type="text" class="form-control" id="banner_text" name="banner_text" value="{{ $hypertargeting->banner_text }}" />
					</div>
				</div>

				<div class="form-group">
					<label for="facts_title" class="col-sm-3 control-label">Facts Title:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="facts_title" name="facts_title" value="{{ $hypertargeting->facts_title }}" />
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label for="facts_body" class="col-sm-3 control-label">Facts Desc:</label>
					<div class="col-sm-8">
						<textarea rows="4" class="form-control" id="facts_body" name="facts_body">{{ $hypertargeting->facts_body }}</textarea>
					</div>
				</div>
			</div>
		</div>

		<h5 class="modal_h5">Fact Blocks</h5>
		@foreach ($hypertargeting->facts as $fact)
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="fact_title_{{ $loop->iteration }}" class="col-sm-3 control-label">Fact Title:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="fact_title_{{ $loop->iteration }}" name="fact_title_{{ $loop->iteration }}" value="{{ $fact->fact_title }}" />
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="fact_body_{{ $loop->iteration }}" class="col-sm-3 control-label">Fact Body:</label>
					<div class="col-sm-8">
						<textarea rows="2" class="form-control" id="fact_body_{{ $loop->iteration }}" name="fact_body_{{ $loop->iteration }}" placeholder="">{{ $fact->fact_body }}</textarea>
					</div>
				</div>
			</div>
		</div>
		@endforeach
	@endif

	@if (in_array('testimonials', $allowed_fields))
		<h5 class="modal_h5">Testimonial Information</h5>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="testimonials_title" class="col-sm-3 control-label">Testimonials Title:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="testimonials_title" name="testimonials_title" value="{{ $hypertargeting->testimonials_title }}" />
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label for="testimonials_body" class="col-sm-3 control-label">Testimonials Desc:</label>
					<div class="col-sm-8">
						<textarea rows="2" class="form-control" id="testimonials_body" name="testimonials_body">{{ $hypertargeting->testimonials_body }}</textarea>
					</div>
				</div>

				@if (in_array('testimonials_background_image', $allowed_fields))
				<div class="form-group">
					<label for="testimonials_background_image" class="col-sm-3 control-label">Testimonial Background Image:</label>
					<div class="col-sm-8 fileupload_pad">
						<input accept=".gif,.jpg,.jpeg,.png,.svg" id="testimonials_background_image"name="testimonials_background_image" type="file" />
						@if ($hypertargeting->testimonials_background_image)
						<div class="current_image">
							<a target="_new" href="{{ $hypertargeting->testimonials_background_image }}">current image</a>
						</div>
					@endif
					</div>
				</div>
				@endif
			</div>
		</div>

		<h5 class="modal_h5">Testimonial Blocks</h5>
		@foreach ($hypertargeting->testimonials as $testimonial)
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="testimonial_name_{{ $loop->iteration }}" class="col-sm-3 control-label">Testimonial Name:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="testimonial_name_{{ $loop->iteration }}" name="testimonial_name_{{ $loop->iteration }}" value="{{ $testimonial->testimonial_name }}" />
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="testimonial_body_{{ $loop->iteration }}" class="col-sm-3 control-label">Testimonial Body:</label>
					<div class="col-sm-8">
						<textarea rows="2" class="form-control" id="testimonial_body_{{ $loop->iteration }}" name="testimonial_body_{{ $loop->iteration }}">{{ $testimonial->testimonial_body }}</textarea>
					</div>
				</div>
			</div>
		</div>
		@endforeach
	@endif

	<h5 class="modal_h5">About Information</h5>
	<div class="row">
		<div class="col-md-6">

			@if (in_array('about_title', $allowed_fields))
			<div class="form-group">
				<label for="about_title" class="col-sm-3 control-label">About Title:</label>
				<div class="col-sm-8">
					<input required type="text" class="form-control" id="about_title" name="about_title" value="{{ $hypertargeting->about_title }}" />
				</div>
			</div>
			@endif

			@if (in_array('about_link', $allowed_fields))
			<div class="form-group">
				<label for="about_link" class="col-sm-3 control-label">About Link:</label>
				<div class="col-sm-8">
					<input required type="text" class="form-control" id="about_link" name="about_link" value="{{ $hypertargeting->about_link }}" />
				</div>
			</div>
			@endif

			@if (in_array('about_image', $allowed_fields))
			<div class="form-group">
				<label for="about_image" class="col-sm-3 control-label">About Image:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.svg" id="about_image" name="about_image" type="file" />
					@if ($hypertargeting->about_image)
						<div class="current_image">
							<a target="_new" href="{{ $hypertargeting->about_image }}">current image</a>
						</div>
					@endif
				</div>
			</div>
			@endif

			@if (in_array('about_logos', $allowed_fields))
			<div class="aboutLogos">
				@for ($i = 1; $i <= $aboutlogocount; $i++)
					<div class="form-group">
					<label for="about_logo_{{ $i }}" class="col-sm-3 control-label">About Logo:</label>
					<div class="col-sm-8 fileupload_pad">
						<input accept=".gif,.jpg,.jpeg,.png,.svg" id="about_logo_{{ $i }}" name="about_logo_{{ $i }}" type="file" />

						@if (!empty($hypertargeting->about_logos[$i - 1]))
						<div class="current_image">
							<a target="_new" href="{{ $hypertargeting->about_logos[$i - 1] }}">current image</a>
						</div>
						@endif
					</div>
				</div>
				@endfor
			</div>
			@endif

		</div>

		<div class="col-md-6">
			@if (in_array('about_body', $allowed_fields))
			<div class="form-group">
				<label for="about_body" class="col-sm-3 control-label">About Body:</label>
				<div class="col-sm-8">
					<textarea required rows="5" class="form-control" id="about_body" name="about_body">{{ $hypertargeting->about_body }}</textarea>
				</div>
			</div>
			@endif

			@if (in_array('about_background_image', $allowed_fields))
			<div class="form-group">
				<label for="about_background_image" class="col-sm-3 control-label">About Background Image:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.svg" id="about_background_image" name="about_background_image" type="file" />
					@if ($hypertargeting->about_background_image)
						<div class="current_image">
							<a target="_new" href="{{ $hypertargeting->about_background_image }}">current image</a>
						</div>
					@endif
				</div>
			</div>
			@endif
		</div>

	</div>

	<h5 class="modal_h5">Learn More Information</h5>
	<div class="row">
		<div class="col-md-6">

			@if (in_array('learnmore_title', $allowed_fields))
			<div class="form-group">
				<label for="learnmore_title" class="col-sm-3 control-label">More Title:</label>
				<div class="col-sm-8">
					<input required type="text" class="form-control" id="learnmore_title" name="learnmore_title" value="{{ $hypertargeting->learnmore_title }}" />
				</div>
			</div>
			@endif

			@if (in_array('learnmore_link', $allowed_fields))
			<div class="form-group">
				<label for="learnmore_link" class="col-sm-3 control-label">More Link:</label>
				<div class="col-sm-8">
					<input required type="text" class="form-control" id="learnmore_link" name="learnmore_link" value="{{ $hypertargeting->learnmore_link }}" />
				</div>
			</div>
			@endif

			@if (in_array('learnmore_image', $allowed_fields))
			<div class="form-group">
				<label for="learnmore_image" class="col-sm-3 control-label">More Image:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.svg" id="learnmore_image" name="learnmore_image" type="file" />
					@if ($hypertargeting->learnmore_image)
						<div class="current_image">
							<a target="_new" href="{{ $hypertargeting->learnmore_image }}">current image</a>
						</div>
					@endif
				</div>
			</div>
			@endif

		</div>

		@if (in_array('learnmore_body', $allowed_fields))
		<div class="col-md-6">
			<div class="form-group">
				<label for="learnmore_body" class="col-sm-3 control-label">More Body:</label>
				<div class="col-sm-8">
					<textarea required rows="5" class="form-control" id="learnmore_body" name="learnmore_body">{{ $hypertargeting->learnmore_body }}</textarea>
				</div>
			</div>
		</div>
		@endif

	</div>

	<h5 class="modal_h5">Miscellaneous Information</h5>
	<div class="row">

		@if (in_array('form_url', $allowed_fields))
		<div class="col-md-6">
			<div class="form-group">
				<label for="form_url" class="col-sm-3 control-label">Form URL:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="form_url" name="form_url" value="{{ $hypertargeting->form_url }}" />
				</div>
			</div>
		</div>
		@endif

		@if (in_array('mobile_form_background_image', $allowed_fields))
		<div class="col-md-6">
			<div class="form-group">
				<label for="mobile_form_background_image" class="col-sm-3 control-label">Mobile Form Background Image:</label>
				<div class="col-sm-8 fileupload_pad">
					<input accept=".gif,.jpg,.jpeg,.png,.svg" id="mobile_form_background_image" name="mobile_form_background_image" type="file" />
					@if ($hypertargeting->mobile_form_background_image)
						<div class="current_image">
							<a target="_new" href="{{ $hypertargeting->mobile_form_background_image }}">current image</a>
						</div>
					@endif
				</div>
			</div>
		</div>
		@endif

		@if (in_array('simplif_submission_url', $allowed_fields))
		<div class="col-md-6">
			<div class="form-group">
				<label for="simplifi_submission_url" class="col-sm-3 control-label">Simplifi Submit URL:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="simplifi_submission_url" name="simplifi_submission_url" value="{{ $hypertargeting->simplifi_submission_url }}" />
				</div>
			</div>
		</div>
		@endif

		@if (in_array('recaptcha_key', $allowed_fields))
		<div class="col-md-6">
			<div class="form-group">
				<label for="recaptchakey" class="col-sm-3 control-label">Recaptcha Key:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="recaptchakey" name="recaptcha_key" value="{{ $hypertargeting->recaptcha_key }}" />
				</div>
			</div>
		</div>
		@endif

		@if (in_array('recaptcha_secret_key', $allowed_fields))
		<div class="col-md-6">
			<div class="form-group">
				<label for="recaptchasecretkey" class="col-sm-3 control-label">Recaptcha Secret:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control fillrandstring" id="recaptchasecretkey" name="recaptcha_secret_key" value="{{ $hypertargeting->recaptcha_secret_key }}" />
				</div>
			</div>
		</div>
		@endif

		@if (in_array('simplifi_url', $allowed_fields))
		<div class="col-md-6">
			<div class="form-group">
				<label for="simplifi_url" class="col-sm-3 control-label">Simplifi URL:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="simplifi_url" name="simplifi_url" value="{{ $hypertargeting->simplifi_url }}" />
				</div>
			</div>
		</div>
		@endif

		@if (in_array('ga_key', $allowed_fields))
		<div class="col-md-6">
			<div class="form-group">
				<label for="gakey" class="col-sm-3 control-label">GA Key:</label>
				<div class="col-sm-8">
					<input required type="text" class="form-control" id="gakey" name="ga_key" value="{{ $hypertargeting->ga_key }}" />
				</div>
		</div>
		</div>
		@endif

		@if (in_array('stripe_secret_key', $allowed_fields))
		<div class="col-md-6">
			<div class="form-group">
				<label for="stripe_secret_key" class="col-sm-3 control-label">Stripe Secret:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control fillrandstring" id="stripe_secret_key" name="stripe_secret_key" value="{{ $hypertargeting->stripe_secret_key }}" />
				</div>
			</div>
		</div>
		@endif

		@if (in_array('stripe_public_key', $allowed_fields))
		<div class="col-md-6">
			<div class="form-group">
				<label for="stripe_public_key" class="col-sm-3 control-label">Stripe Public:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control fillrandstring" id="stripe_public_key" name="stripe_public_key" value="{{ $hypertargeting->stripe_public_key }}" />
				</div>
			</div>
		</div>
		@endif

		@if (in_array('bbox_form_id', $allowed_fields))
		<div class="col-md-6">
			<div class="form-group">
				<label for="bbox_form_id" class="col-sm-3 control-label">Blackbaud ID:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control fillrandstring" id="bbox_form_id" name="bbox_form_id" value="{{ $hypertargeting->bbox_form_id }}" />
				</div>
			</div>
		</div>
		@endif

	</div>

	<h5 class="modal_h5">More Information</h5>
	<div class="row">

		@if (in_array('privacy_policy', $allowed_fields))
		<div class="col-md-6">
			<div class="form-group">
				<div class="col-sm-12">
					<label for="privacy_policy" class="control-label">Privacy Policy:</label>
					<textarea rows="3" class="form-control" id="privacy_policy" name="privacy_policy" placeholder="">{{ $hypertargeting->privacy_policy }}</textarea>
				</div>
			</div>
		</div>
		@endif

		@if (in_array('contact_block', $allowed_fields))
		<div class="col-md-6">
			<div class="form-group">
				<div class="col-sm-12">
					<label for="contact_block" class="control-label">Contact Block:</label>
					<textarea rows="3" class="form-control" id="contact_block" name="contact_block" placeholder="">{{ $hypertargeting->contact_block }}</textarea>
				</div>
			</div>
		</div>
		@endif

		@if (in_array('form_body_text', $allowed_fields))
		<div class="col-md-6">
			<div class="form-group">
				<div class="col-sm-12">
					<label for="form_body_text" class="control-label">Text Above Donation Form:</label>
					<textarea rows="3" class="form-control" id="form_body_text" name="form_body_text" placeholder="">{{ $hypertargeting->form_body_text }}</textarea>
				</div>
			</div>
		</div>
		@endif

	</div>


	<div class="form-group">
		<div class="col-sm-9 fileupload_pad">
			<button id="add_btn" type="submit" class="btn btn-primary">Update</button>
			<button data-href="/hypertargeting/duplicate/{{ $hypertargeting->id }}/" id="duplicate_btn" type="button" class="btn btn-secondary">Duplicate</button>
			<button data-href="/hypertargeting/delete/{{ $hypertargeting->id }}" id="delete_btn" type="button" class="btn btn-primary btn-delete">Delete HyperTargeting</button>
		</div>
	</div>
</form>
