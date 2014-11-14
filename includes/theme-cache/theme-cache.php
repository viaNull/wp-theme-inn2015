<?php
/*
Feature Name:	theme-cache
Feature URI:	http://inn-studio.com
Version:		1.0.5
Description:	theme-transient
Author:			INN STUDIO
Author URI:		http://inn-studio.com
*/

theme_cache::init();
class theme_cache{
	public static $cache_expire = 3600;
	public static $iden = 'theme-cache';
	public static function init(){
		if(!class_exists('phpFastCache')) include dirname(__FILE__) . '/inc/php_fast_cache.php';
		phpFastCache::$path = WP_CONTENT_DIR . '/';
		phpFastCache::$autosize = 128;
		phpFastCache::$files_cleanup_after = 24*29;

		add_action('base_settings',get_class() . '::backend_display');
		add_action('wp_ajax_' . self::$iden, get_class() . '::process');
		add_action('save_post',function(){
			self::delete('queries');
		});
		add_action('delete_post',function(){
			self::delete('queries');
		});
	}
	private static function get_process_url($type){
		return theme_features::get_process_url(array(
			'action' => self::$iden,
			'return' => get_current_url() . '&' .self::$iden . '=1',
			'type' => $type
			
		));
	}
	/**
	 * Admin Display
	 */
	public static function backend_display(){
		$options = theme_options::get_options();

		?>
		<fieldset>
			<legend><?php echo ___('Clean theme cache');?></legend>
			<p class="description"><?php echo ___('Maybe the theme used cache for improve performance, you can clean it when you modify some site contents if you want.');?></p>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php echo ___('Control');?></th>
						<td>
							<?php
							if(isset($_GET[self::$iden])){
								echo status_tip('success',___('Theme cache has been cleaned or rebuilt.'));
							}
							?>
							<p>
								<a href="<?php echo self::get_process_url('all');?>" class="button" onclick="javascript:this.innerHTML='<?php echo ___('Processing, please wait...');?>'"><?php echo ___('Clean all cache');?></a>
								
								<a href="<?php echo self::get_process_url('widget');?>" class="button" onclick="javascript:this.innerHTML='<?php echo ___('Processing, please wait...');?>'"><?php echo ___('Clean widget cache');?></a>
								
								<a href="<?php echo self::get_process_url('menu');?>" class="button" onclick="javascript:this.innerHTML='<?php echo ___('Processing, please wait...');?>'"><?php echo ___('Clean menu cache');?></a>
								
								
								<span class="description"><?php echo ___('Save your settings before clean');?></span>
								
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	<?php
	}
	/**
	 * process
	 */
	public static function process(){
		$output = null;
		$type = isset($_GET['type']) ? $_GET['type'] : null;
		if(isset($_GET['return'])){
			switch($type){
				case 'widget':
					self::delete('widget-sidebars');
				break;
				case 'menu':
					self::delete('nav-menus');
				break;
				default:
					self::cleanup();
				break;
			}
			wp_redirect($_GET['return']);
			die();
		}
		die(theme_features::json_format($output));
	}
	public static function cleanup(){
		phpFastCache::cleanup();
	}
	/**
	 * Delete cache
	 *
	 * @param string $key Cache key
	 * @return false
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 */
	public static function delete($key){
		phpFastCache::delete($key . AUTH_KEY);
	}
	/**
	 * Set cache
	 *
	 * @param string $key Cache ID
	 * @param mixed $value Cache contents
	 * @return int $expire Cache expire time (s)
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 */
	public static function set($key,$value,$expire = 3600){
		return phpFastCache::set($key . AUTH_KEY,$value,$expire);
	}
	/**
	 * Get the cache
	 *
	 * @param string $key Cache ID
	 * @return mixed
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 */
	public static function get($key){
		return phpFastCache::get($key . AUTH_KEY);
	}
	/**
	 * Get comments 
	 *
	 * @param string $id The cache id
	 * @param int $expire Cache expire time
	 * @return mixed
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 */
	public static function get_comments($args,$expire = 3600){
		$cache_group_id = 'comments';
		$id = md5(serialize($args));
		$cache = (array)self::get($cache_group_id);
		if(empty($cache) || !isset($cache[$id])){
			$cache[$id] = get_comments($args);
			self::set($cache_group_id,$cache,$expire);
		}
		return $cache[$id];
	}
	/**
	 * Get queries 
	 *
	 * @param string $id The cache id
	 * @param int $expire Cache expire time
	 * @return mixed
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 */
	public static function get_queries($args,$expire = 3600){
		$cache_group_id = 'queries';
		$id = md5(serialize($args));
		$cache = (array)self::get($cache_group_id);
		if(empty($cache) || !isset($cache[$id])){
			$cache[$id] = new WP_Query($args);
			self::set($cache_group_id,$cache,$expire);
		}
		return $cache[$id];
	}
	/**
	 * Get user data by field and data
	 *
	 * @param string 'id','slug','email','login'
	 * @param int/string
	 * @return mixed success returns object, otherwise false
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 */
	public static function get_user_by($field,$value){
		$cache_author_datas = (array)self::get('users');
		$cache_id = md5(serialize(array($field,$value)));
		if(empty($cache_author_datas) || !isset($cache_author_datas[$cache_id])){
			$user = get_user_by($field,$value);
			// var_dump($user,$field,$value);
			$cache_author_datas[$cache_id] = $user;
			// $cache_author_datas[$cache_id]->metas = get_user_meta($user->ID);
			self::set('users',$cache_author_datas,self::$cache_expire);
		}
		return $cache_author_datas[$cache_id];

	}
	/**
	 * Get widget sidebars from cache
	 *
	 * @param string The widget sidebar name/id
	 * @param int Cache expire time
	 * @return string
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 */
	public static function get_widget_sidebars($id,$expire = 3600){
		$cache_group_id = 'widget-sidebars';
		$cache = (array)self::get($cache_group_id);
		if(is_singular()){
			global $post;
			$cache_id_pre = 'post-' . $post->ID;
		}else if(is_home()){
			$cache_id_pre = 'home';
		}else if(is_category()){
			$cache_id_pre = 'cat-' . theme_features::get_current_cat_id();
		}else if(is_tag()){
			$cache_id_pre = 'cat-' . theme_features::get_current_tag_id();
		}else if(is_search()){
			$cache_id_pre = 'search';
		}else{
			$cache_id_pre = theme_features::get_wp_title();
		}
		$cache_id = md5($cache_id_pre . $id);
		if(empty($cache) || !isset($cache[$cache_id])){
			ob_start();
			dynamic_sidebar($id);
			$content = html_compress(ob_get_contents());
			ob_end_clean();
			
			$cache[$cache_id] = $content;
			self::set($cache_group_id,$cache,$expire);
		}
		return $cache[$cache_id];
	}
	/**
	 * Get nav menu from cache
	 *
	 * @param string The widget sidebar name/id
	 * @param int Cache expire time
	 * @return string
	 * @version 1.0.1
	 * @author KM@INN STUDIO
	 */
	public static function get_nav_menu($args,$expire = 3600){
		$defaults = array(
			'theme_location' => null,
			'menu_class' => null,
			'container' => 'nav',
		);
		$r = wp_parse_args($args,$defaults);
		$cache_group_id = 'nav-menus';
		
		if(is_singular()){
			global $post;
			$id = 'post-' . $post->ID;
		}else if(is_home()){
			$id = 'home';
		}else if(is_category()){
			$id = 'cat-' . theme_features::get_current_cat_id();
		}else if(is_tag()){
			$id = 'cat-' . theme_features::get_current_tag_id();
		}else if(is_search()){
			$id = 'search';
		}else{
			$id = theme_features::get_wp_title();
		}
		$id = md5(serialize($r) . $id);
		$cache = (array)self::get($cache_group_id);
		if(empty($cache) || !isset($cache[$id])){
			ob_start();
			wp_nav_menu($r);
			$content = html_compress(ob_get_contents());
			ob_end_clean();
			
			$cache[$id] = $content;
			self::set($cache_group_id,$cache,$expire);
		}
		return $cache[$id];
	}
}
?>