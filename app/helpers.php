<?php

	function format_user_role($role_id) {

		$role_name = "";

		switch ($role_id) {
			case 1:
				$role_name = "IDD User";
				break;
			case 2:
				$role_name = "Soar User";
				break;
			case 20:
				$role_name = "IDD Manager";
				break;
			case 21:
				$role_name = "Soar Manager";
				break;
			case 99:
				$role_name = "Superuser";
				break;
		}

		return $role_name;
	}

	function format_business_status($status) {

		$status_name = "";

		switch ($status) {
			case -1:
				$status_name = "Closed";
				break;
			case 0:
				$status_name = "New";
				break;
			case 1:
				$status_name = "Ready";
				break;
			case 10:
				$status_name = "Claimed";
				break;
			case 20:
				$status_name = "Contacted";
				break;
		}

		return $status_name;
	}

	function format_bool_yn($bool) {

		$answer = "";

		switch ($bool) {
			case 1:
				$answer = "Yes";
				break;
			case 0:
				$answer = "No";
				break;
		}

		return $answer;
	}

	function sms_status($bool) {

		$answer = "";

		switch ($bool) {
			case 1:
				$answer = "Active";
				break;
			case 0:
				$answer = "Inactive";
				break;
		}

		return $answer;
	}

	function ht_status($status) {

		$status_name = "";

		switch ($status) {
			case 1:
				$status_name = "Completed";
				break;
			case 0:
				$status_name = "Processing";
				break;
		}

		return $status_name;

	}

	function rander($length=10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	function create_html_link($url, $text, $class = '', $target = '_self') {
	    return "<a class=\"{$class}\" href=\"{$url}\" target=\"{$target}\">{$text}</a>";
    }

    function create_user_link($user, $class = '', $target = '_self') {
	    return create_html_link("/users/view/{$user->id}", $user->name, $target);
    }

	function filter_array_by_keys($array, $allowed_keys) {
        return array_filter(
            $array,
            function ($key) use ($allowed_keys) {
                return in_array($key, $allowed_keys);
            },
            ARRAY_FILTER_USE_KEY
        );
	}
	
	function object_to_array($object) {
		return json_decode(json_encode($object), true);
	}
	function hypertargeting_domain($domain, $default) {
		if ($domain) {
			return $domain;
		} else {
			return $default;
		}
	}
