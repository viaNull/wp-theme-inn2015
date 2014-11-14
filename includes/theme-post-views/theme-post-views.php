<?php
/*
Feature Name:	Post Views
Feature URI:	http://www.inn-studio.com
Version:		1.1.10
Description:	Count the post views.
Author:			INN STUDIO
Author URI:		http://www.inn-studio.com
*/
theme_post_views::init();
class theme_post_views{
	private static $iden = 'theme_post_views';
	private static $log_key = 'views';
	private static $cookie_key = 'vids';
	private static $cookie_expire = 2592000;/** 30 days */
	private static $cache_expire = 2505600;/** memcache max 29 days */
	private static $storage_times = 10;

	public static function init(){
		// add_action('page_settings',get_class() . '::backend_display');
		// add_action('theme_options_save',get_class() . '::options_save');
		// if(!self::is_enabled()) return;
		add_filter('cache-request',get_class() . '::cache_request');
		add_filter('js-cache-request',get_class() . '::js_cache_request',1);
		
		add_action('admin_head', get_class() . '::admin_css');
		add_action('manage_posts_custom_column',get_class() . '::admin_show',10,2);
		// add_action('save_post', get_class() . '::save_post');
		add_filter('manage_posts_columns', get_class() . '::admin_add_column');
	}
	public static function save_post($post_id){
		/**
		 * check the revision
		 */
		if ($parent_id = wp_is_post_revision($post_id)) {
			$post_id = $parent_id;
		}
		/**
		 * update the meta
		 */
		add_post_meta($post_id,self::$log_key,1,true);
	}

	/**
	 * update_views
	 * 
	 * 
	 * @return 
	 * @version 1.0.2
	 * @author KM@INN STUDIO
	 * 
	 */
	public static function update_views($post_id = null){
		global $post;
		$post_id = $post_id ? (int)$post_id : $post->ID;
		$cache = (array)theme_cache::get(self::$iden);
		$old_meta_view = (int)get_post_meta($post_id,self::$log_key,true);
		/** 
		 * make sure is not revision post
		 */
		if(wp_is_post_revision($post_id) == false){
			$tmp_view = isset($cache[$post_id]) ? (int)$cache[$post_id] : 0;
			++$tmp_view;
			/** 
			 * max storage time, save to database
			 */
			if(self::is_max_storage_time($post_id)){
				$new_meta_view = $old_meta_view + $tmp_view;
				update_post_meta($post_id,self::$log_key,(int)$new_meta_view);
				/** 
				 * reset tmp_view
				 */
				$cache[$post_id] = 0;
			}else{
				$cache[$post_id] = $tmp_view;
			}
			theme_cache::set(self::$iden,$cache,self::$cache_expire);
			return self::get_view_from_cache($post_id);
		}else{
			return false;
		}
	}
	public static function is_viewed($post_id = null){
		global $post;
		$post_id = $post_id ? (int)$post_id : $post->ID;
		$cookie_view_ids = isset($_COOKIE[self::$cookie_key]) ? @unserialize($_COOKIE[self::$cookie_key]) : null;
		$is_viewed = false;
		if(is_array($cookie_view_ids)){
			if(in_array($post_id,$cookie_view_ids)){
				$is_viewed = true;
			}else{
				$cookie_view_ids[] = $post_id;
			}
		}else{
			$cookie_view_ids = array($post_id);
		}
		/** 
		 * set cookie
		 */
		if(!$is_viewed){
			$expire = time()+ self::$cookie_expire;
			setcookie(self::$cookie_key,serialize($cookie_view_ids),$expire);
		}
		return $is_viewed;
	}
	/**
	 * display
	 * show the views
	 * 
	 * @params int $post_id
	 * @return int the views
	 * @version 1.0.1
	 * @author KM@INN STUDIO
	 * 
	 */
	public static function display($post_id = null){
		global $post;
		$post_id = $post_id ? (int)$post_id : $post->ID;
		return self::get_view_from_cache($post_id);
	}
	public static function is_enabled(){
		// $options = theme_options::get_options();
		// $is_enabled = (isset($options[self::$iden]) && isset($options[self::$iden]['on'])) ? true : false;
		// return $is_enabled;
		return true;
	}
	public static function options_save($options){
		if(isset($_POST[self::$iden])){
			$options[self::$iden] = $_POST[self::$iden];
		}
		return $options;
	}
	public static function backend_display(){
		
		$options = theme_options::get_options();
		$is_checked = self::is_enabled() ? ' checked ' : null;
		?>
		<fieldset>
			<legend><?php echo ___('Post views settings');?></legend>
			<p class="description"><?php echo ___('Count your post views.');?></p>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="post-views-enable"><?php echo ___('Enable or not?');?></label></th>
						<td>
							<input type="checkbox" id="post-views-enable" name="<?php echo self::$iden;?>[on]" value="1" <?php echo $is_checked;?> /><label for="post-views-enable"><?php echo ___('Enable');?></label>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
			
		<?php
	}
	private static function is_max_storage_time($post_id){
		$cache = (array)theme_cache::get('theme_post_views');
		$tmp_view = isset($cache[$post_id]) ? (int)$cache[$post_id] : 0;
		/** 
		 * add a new view number
		 */
		++$tmp_view;
		/** 
		 * Exceed the maximum, update meta
		 */
		if($tmp_view >= self::$storage_times){
			return true;
		}else{
			return false;
		}

	}
	public static function get_view_from_cache($post_id){
		$cache = (array)theme_cache::get('theme_post_views');
		$tmp_view = isset($cache[$post_id]) ? (int)$cache[$post_id] : 0;
		$old_meta_view = (int)get_post_meta($post_id,self::$log_key,true);
		$view = $old_meta_view + $tmp_view;
		return (int)$view;
	}
	/**
	 * process
	 * 
	 * 
	 * @return array
	 * @version 1.0.2
	 * @author KM@INN STUDIO
	 * 
	 */
	public static function cache_request($output){
		if(isset($_GET[self::$iden]) && (int)$_GET[self::$iden] != 0){
			$post_id = (int)$_GET[self::$iden];
			/** 
			 * check is viewed
			 */
			if(!self::is_viewed($post_id)){
				self::update_views($post_id);
			}
			$output[self::$iden] = self::get_view_from_cache($post_id);
		}
		return $output;
	}
	public static function js_cache_request($data){
		if(!is_singular()) return $data;
		global $post;
		$data[self::$iden] = $post->ID;
		return $data;
	}
	/**
	 * admin_add_column
	 * 
	 * 
	 * @params 
	 * @return 
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 * 
	 */
	public static function admin_add_column($columns){
		
		$columns[self::$log_key] = ___('Views');
		return $columns;
	}
	public static function admin_show($column_name,$id){
		if ($column_name != 'views') return;	
		$views = get_post_meta($id,self::$log_key,true);
		echo (int)$views;
	}
	public static function admin_css(){
		?><style>.fixed .column-views{width:3em}</style><?php
	}
}
?>