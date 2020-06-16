<div class="collapse navbar-collapse" id="app-navbar-collapse">
	<ul class="nav navbar-nav main_nav">
		@if (!Auth::guest())
			@if (!Auth::user()->hasRole(['SoarUser','SoarManager']))
				<li class="@if (Request::is('clients')){{ "active" }}@endif"><a href="{{ url('/clients') }}"><i class="fa fa-star" aria-hidden="true"></i> &nbsp;Clients</a></li>
				<li class="@if (Request::is('installs')){{ "active" }}@endif"><a href="{{ url('/installs') }}"><i class="fa fa-server" aria-hidden="true"></i> &nbsp; Installs</a></li>
				<li class="@if (Request::is('nodes')){{ "active" }}@endif"><a href="{{ url('/nodes') }}"><i class="fa fa-cloud" aria-hidden="true"></i> &nbsp; Nodes</a></li>
			@endif

			<li class="@if (Request::is('businesses')){{ "active" }}@endif"><a href="{{ url('/businesses') }}"><i class="fa fa-building" aria-hidden="true"></i> &nbsp; Businesses</a></li>
			<li class="@if (Request::is('hypertargeting')){{ "active" }}@endif"><a href="{{ url('/hypertargeting') }}"><i class="fa fa-bullseye" aria-hidden="true"></i> &nbsp; HyperTargeting</a></li>
			{{--<li class="@if (Request::is('adas')){{ "active" }}@endif"><a href="{{ url('/adas') }}"><i class="fa fa-flag-checkered" aria-hidden="true"></i> &nbsp; ADA</a></li>--}}
			{{--<li class="@if (Request::is('leads')){{ "active" }}@endif"><a href="{{ url('/leads') }}"><i class="fa fa-usd" aria-hidden="true"></i> &nbsp; Leads</a></li>--}}

			@if (!Auth::user()->hasRole(['SoarUser','SoarManager']))
				<li class="dropdown user_menu"><a class="dropdown-toggle" data-toggle="dropdown" href="#">
					<i class="fa fa-user" aria-hidden="true"></i> &nbsp; People</a>
					<ul class="dropdown-menu mm-dd" role="menu">
						@if (!Auth::user()->hasRole(['IDDUser']))
							<li class="@if (Request::is('users')){{ "active" }}@endif"><a href="{{ url('/users') }}">Users</a></li>
						@endif
						<li class="@if (Request::is('owners')){{ "active" }}@endif"><a href="{{ url('/owners') }}">Owners</a></li>
					</ul>
				</li>
				@if (!Auth::user()->hasRole(['IDDUser']))
					<li class="dropdown user_menu @if (Request::is('tools')){{ "active" }}@endif"><a class="dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="fa fa-cog" aria-hidden="true"></i> &nbsp; Tools</a>
						<ul class="dropdown-menu mm-dd" role="menu">
							<li><a href="/tools/plugins">Plugins</a></li>
							<li><a href="/tools/repos">Repos</a></li>
							<li><a href="/tools/repo_types">Repo Types</a></li>
							<li><a href="/tools/client-categories">Client Categories</a></li>
							<li><a href="/tools/roles">Roles</a></li>
							<li><a href="/tools/sms_conversations">SMS Conversations</a></li>
							<li><a href="/tools/messaging_services">Messaging Services</a></li>
						</ul>
					</li>
				@endif
			@endif
		@endif
	</ul>
	<ul class="nav navbar-nav navbar-right">
		@if (Auth::guest())
			<li><a href="{{ url('/login') }}">Login</a></li>
		@else
			<li class="dropdown user_menu">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
					<i>Hello,</i> <strong>{{ Auth::user()->name }}</strong>
					&nbsp;
					<img class="user_ico" width="30" src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(Auth::user()->email))) }}?d={{ urlencode('https://brain.iddigital.me/images/' . Session::get('user_brand') .'_user.png') }}" />
				</a>

				<ul class="dropdown-menu" role="menu">
					<li><a href="/user_profile">Profile</a></li>
					<li>
						<a href="{{ url('/logout') }}"
							onclick="event.preventDefault();
									 document.getElementById('logout-form').submit();">
							Logout
						</a>
						<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
							{{ csrf_field() }}
						</form>
					</li>
				</ul>
			</li>
		@endif
	</ul>
</div>
