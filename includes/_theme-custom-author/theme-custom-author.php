<?php
theme_custom_author::init();
class theme_custom_author{
	public static $iden = 'theme_custom_author';
	public static function init(){
		add_filter('query_vars',					get_class() . '::filter_query_vars');
		add_filter('header_banner',					get_class() . '::header_banner');
		add_filter('frontend_seajs_alias',			get_class() . '::frontend_seajs_alias');
		add_action('frontend_seajs_use',			get_class() . '::frontend_seajs_use');
		add_action('wp_ajax_' . get_class(),		get_class() . '::process');
		
	}
	public static function filter_query_vars($vars){
		if(!in_array('tab',$vars)) $vars[] = 'tab';
		return $vars;
	}
	public static function header_banner($tx){
		if(!is_author()) return $tx;
		global $author;
		$author_data = get_userdata($author);
		$tab_curr = get_query_var('tab');
		$tx =  '<a href="' . esc_url(get_author_posts_url($author)) . '"><span class="icon-user"></span><span class="after-icon">' . esc_html($author_data->display_name) . '</span><span class="des"> - ' . esc_html(___('Member')) . '</span></a>';
		return $tx;
	}
	public static function get_tabs($author_id,$key = null){
		$baseurl = get_author_posts_url($author_id);
		$tabs = array(
			'profile' => array(
				'text' => ___('Profile'),
				'icon' => 'profile',
				'url' => add_query_arg(array(
					'tab' => 'profile'
				),$baseurl),
			),
			'posts' => array(
				'text' => ___('Posts'),
				'icon' => 'stackoverflow',
				'url' => add_query_arg(array(
					'tab' => 'posts'
				),$baseurl),
			),
		);
		/** 
		 * edit
		 */
		if(is_user_logged_in() && $author_id == get_current_user_id()){
			$tabs['edit'] = array(
				'text' => ___('Edit'),
				'icon' => 'compose',
				'url' => add_query_arg(array(
					'tab' => 'edit'
				),$baseurl)
			);
		}
		
		if($key){
			return isset($tabs[$key]) ? $tabs[$key] : false;
		}else{
			return $tabs;
		}
	}
	public static function process(){
		$output = array();
		theme_features::check_referer();
		theme_features::check_nonce();
		$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;
		
		switch($type){
			/** 
			 * change-profile
			 */
			case 'change-profile':
				$post_user = isset($_POST['user']) ? (array)$_POST['user'] : null;
				if(!$post_user){
					$output['status'] = 'error';
					$output['id'] = 'invalid_param';
					$output['msg'] = ___('Invalid param.');
					die(theme_features::json_format($output));
				}
				$post_nickname = isset($post_user['nickname']) ? trim($post_user['nickname']) : null;
				/** nickname */
				if(empty($post_nickname)){
					$output['status'] = 'error';
					$output['id'] = 'invalid_nickname';
					$output['msg'] = ___('Invalid nickname.');
					die(theme_features::json_format($output));
				}
				$post_url = isset($post_user['url']) ? esc_url($post_user['url']) : null;
				$post_des = isset($post_user['description']) ? trim($post_user['description']) : null;
				global $current_user;
				get_currentuserinfo();
				$user_id = wp_update_user(array(
					'ID' => $current_user->ID,
					'user_nicename' => $current_user->ID,
					'display_name' => $post_nickname,
					'nickname' => $post_nickname,
					'description' => $post_des,
					'user_url' => $post_url,
				));
				if(is_wp_error($user_id)){
					$output['status'] = 'error';
					$output['id'] = $user_id->get_error_code();
					$output['msg'] = $user_id->get_error_message();
					die(theme_features::json_format($output));
				}else{
					$output['status'] = 'success';
					$output['msg'] = ___('Your profile data has been updated.');
				}
				break;
			/** 
			 * change password
			 */
			case 'change-pwd':
				$post_user = isset($_POST['user']) ? (array)$_POST['user'] : null;
				if(!$post_user){
					$output['status'] = 'error';
					$output['id'] = 'invalid_param';
					$output['msg'] = ___('Invalid param.');
					die(theme_features::json_format($output));
				}
				$pwd_curr = isset($_POST['pwd-curr']) ? $_POST['pwd-curr'] : null;
				global $current_user;
				get_currentuserinfo();
				$current_user_id = $current_user->ID;
				/** confirm current password */
				if(empty($pwd_curr) || wp_hash_password($pwd_curr) != $current_user->user_pass){
					$output['status'] = 'error';
					$output['id'] = 'invalid_curr_pwd';
					$output['msg'] = ___('Current password is invalid, please try again.');
					die(theme_features::json_format($output));
				}
				/** check twice password */
				$pwd_new = isset($_POST['pwd-new']) ? $_POST['pwd-new'] : null;
				$pwd_again = isset($_POST['pwd-again']) ? $_POST['pwd-again'] : null;
				if(empty($pwd_new) || empty($pwd_again) || $pwd_new !== $pwd_again){
					$output['status'] = 'error';
					$output['id'] = 'invalid_twice_pwd';
					$output['msg'] = ___('Twice password is invalid, please try again.');
					die(theme_features::json_format($output));
				}
				
				wp_set_password($pwd_new,$current_user_id);
				/** relogin again */
				$user = get_userdata($current_user_id);
				wp_set_current_user($current_user_id);
				wp_set_auth_cookie($current_user_id);
				do_action('wp_login',$user->user_login);
				
				$output['status'] = 'success';
				// $output['redirect'] = get_permalink(get_page_by_slug(theme_custom_sign::$page_slug)->ID);
				$output['msg'] = ___('Your password has been updated.');
				
				break;
		
		}
		die(theme_features::json_format($output));
	}

	public static function frontend_seajs_alias($alias){
		if(is_user_logged_in() && 
			is_author(get_current_user_id()) && 
			get_query_var('tab') === 'edit')
		{
			$alias['author-edit'] = theme_features::get_theme_includes_js(__FILE__,'edit');
		}

		// $alias[self::$iden] = theme_features::get_theme_includes_js(__FILE__);
		return $alias;
	}
	
	public static function frontend_seajs_use(){
		if(is_user_logged_in() && 
			is_author(get_current_user_id()) && 
			get_query_var('tab') === 'edit')
		{
			?>
			seajs.use('author-edit',function(m){
				m.config.process_url = '<?php echo theme_features::get_process_url(array('action' => self::$iden));?>';
				m.config.lang.M00001 = '<?php echo esc_js(___('Loading, please wait...'));?>';
				m.config.lang.E00001 = '<?php echo esc_js(___('Sorry, server error please try again later.'));?>';
				
				m.init();
			});
			<?php
		}
	}

}