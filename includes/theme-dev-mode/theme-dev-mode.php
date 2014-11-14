<?php
/*
Feature Name:	Developer mode
Feature URI:	http://www.inn-studio.com
Version:		1.1.4
Description:	启用开发者模式，助于维护人员进行调试，运营网站请禁用此模式
Author:			INN STUDIO
Author URI:		http://www.inn-studio.com
*/
theme_dev_mode::init();
class theme_dev_mode{
	private static $iden = 'theme_dev_mode';
	private static $data = array();
	
	public static function init(){
		add_filter('theme_options_save',get_class() . '::save');
		add_action('after_setup_theme',get_class() . '::mark_start_data',0);
		add_action('wp_footer',get_class() . '::hook_footer',9999);
		add_action('dev_settings',get_class() . '::admin');
	}

	public static function mark_start_data(){
		// if(!self::is_enabled()) return false;
		self::$data = array(
			'start-time' => timer_stop(0),
			'start-query' => get_num_queries(),
			'start-memory' => sprintf('%01.3f',memory_get_usage()/1024/1024)
		);
	}
	public static function is_enabled(){
		$options = theme_options::get_options();
		if(isset($options['dev_mode']) && isset($options['dev_mode']['on'])){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * admin
	 * 
	 * 
	 * @return n/a
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 * 
	 */
	public static function admin(){
		
		$options = theme_options::get_options();
		?>
		<fieldset>
			<legend><?php echo ___('Related Options');?></legend>
			<p class="description"><?php echo ___('For developers to debug the site and it will affect the user experience if enable, please note.');?></p>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="dev_mode"><?php echo ___('Developer mode');?></label>
						</th>
						<td>
							<label for="dev_mode"><input id="dev_mode" name="dev_mode[on]" type="checkbox" value="1" <?php if(isset($options['dev_mode']['on'])){echo 'checked="checked"';} ?> /> <?php echo ___('Enabled');?></label>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset>
			<legend><?php echo ___('Theme Options');?></legend>
			<div class="description">
				<textarea class="text-code" cols="50" rows="20" style="width:99%;height:50em"><?php print_r($options);?></textarea>
			</div>
		</fieldset>
		<?php
	}

	/**
	 * save
	 * 
	 * 
	 * @params array
	 * @return array
	 * @version 1.0.1
	 * @author KM@INN STUDIO
	 * 
	 */
	public static function save($options){
		$old_options = theme_options::get_options();
		if(isset($old_options['dev_mode']) && isset($old_options['dev_mode']['on']) && !isset($_POST['dev_mode']['on'])){
			ini_set('max_input_nesting_level','10000');
			ini_set('max_execution_time','300'); 
			
			remove_dir(get_stylesheet_directory() . theme_features::$basedir_js_min);
			remove_dir(get_stylesheet_directory() . theme_features::$basedir_css_min);
			
			theme_features::minify_force(get_stylesheet_directory() . theme_features::$basedir_js_src);
			theme_features::minify_force(get_stylesheet_directory() . theme_features::$basedir_css_src);
			theme_features::minify_force(get_stylesheet_directory() . theme_features::$basedir_includes);
		}
		$options['dev_mode'] = isset($_POST['dev_mode']['on']) ? $_POST['dev_mode'] : null;
		return $options;
	}


	/**
	 * compress
	 *
	 * @return 
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 */
	public static function compress(){
		$options = theme_options::get_options();
		/** 
		 * if dev_mode is off
		 */
		if(!self::is_enabled() && !is_admin()){
			ob_start('html_compress');
		}
	}
	public static function hook_footer(){
		$options = theme_options::get_options();
		// if(!self::is_enabled()) return false;
		
		?>
		<script>
		try{
			<?php
			self::$data['end-time'] =  timer_stop(0);
			self::$data['end-query'] = get_num_queries();
			self::$data['end-memory'] = sprintf('%01.3f',memory_get_usage()/1024/1024);
			
			self::$data['theme-time'] = self::$data['end-time'] - self::$data['start-time'];
			self::$data['theme-query'] = self::$data['end-query'] - self::$data['start-query'];
			self::$data['theme-memory'] = self::$data['end-memory'] - self::$data['start-memory'];

			$data = array(
				___('Theme Performance') => array(
					___('Time (second)') => self::$data['theme-time'],
					___('Query') => self::$data['theme-query'],
					___('Memory (MB)') => self::$data['theme-memory'],
				),
				___('Basic Performance') => array(
					___('Time (second)') => (float)self::$data['start-time'],
					___('Query') => (float)self::$data['start-query'],
					___('Memory (MB)') => (float)self::$data['start-memory'],
				),
				___('Final Performance') => array(
					___('Time (second)') => (float)self::$data['end-time'],
					___('Query') => (float)self::$data['end-query'],
					___('Memory (MB)') => (float)self::$data['end-memory'],
				),
			);
			?>
			(function(){
				var data = <?php echo json_encode($data);?>;
				console.table(data);
			})();
		}catch(e){}
		</script>
		<?php
	}
}
?>