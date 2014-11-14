<?php
add_filter('get_avatar', 'theme_gravatar_fix::get_gravatar');
class theme_gravatar_fix{
	public static function get_gravatar($avatar) {
		$avatar = preg_replace("/http:\/\/([0-9])\.gravatar\.com\/avatar/i", "https://cdn.v2ex.com/gravatar", $avatar);
		return $avatar;
	}
}