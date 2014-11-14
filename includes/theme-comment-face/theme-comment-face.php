<?php
/*
Feature Name:	theme_comment_face
Feature URI:	http://inn-studio.com
Version:		1.0.1
Description:	
Author:			INN STUDIO
Author URI:		http://inn-studio.com
*/
// theme_comment_face::init();
class theme_comment_face{
	public static $iden = 'theme_comment_face';
	public static $key_style = 'theme-comment-face';

	public static function init(){
		add_filter('theme_options_save',get_class() . '::options_save');
		add_filter('theme_options_default',get_class() . '::options_default');
		add_action('frontend_seajs_use',get_class() . '::frontend_seajs_use');
		add_action('page_settings',get_class() . '::options_display');
		add_filter('get_comment_text',get_class() . '::filter_comment_output');
		
	}
	public static function options_default($options){
		$options[self::$iden] = array(
			'emoticons' => array(
				'(⊙⊙！) ',
				'ƪ(‾ε‾“)ʃƪ(',
				'Σ(°Д°;',
				'눈_눈',
				'(๑>◡<๑) ',
				'(❁´▽`❁)',
				'(,,Ծ▽Ծ,,)',
				'（⺻▽⺻ ）',
				'乁( ◔ ౪◔)「',
				'ლ(^o^ლ)',
				'(◕ܫ◕)',
				'凸(= _=)凸'
			)
		);
		return $options;
	}
	public static function options_save($options){
		$emoticons = isset($_POST[self::$iden]['emoticons']) ? $_POST[self::$iden]['emoticons'] : null;
		if($emoticons){
			$options[self::$iden] = array(
				'emoticons' => array_filter(explode(PHP_EOL,$emoticons))
			);
		}
		return $options;
	}
	public static function options_display(){
		
		$options = theme_options::get_options();
		$emoticons = (array)self::get_emoticons();
		?>
		<fieldset>
			<legend><?php echo ___('Face settings');?></legend>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="<?php echo self::$iden;?>-emoticons"><?php echo ___('Theme comment face emoticons (one per line)');?></label></th>
						<td>
							<textarea name="<?php echo self::$iden;?>[emoticons]" id="<?php echo self::$iden;?>-emoticons" cols="30" rows="10" class="widefat"><?php echo implode(PHP_EOL,$emoticons);?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<?php
	}
	public static function get_emoticons(){
		$options = theme_options::get_options();
		$emoticons = isset($options[self::$iden]['emoticons'])
			? array_map('stripslashes',$options[self::$iden]['emoticons']) : array();
		return $emoticons;
	}
	public static function filter_comment_output($comment_text){
		$pattern = '/\[([a-z]+\-[0-9]+\.[a-z]+)\]/i';
		$comment_text = preg_replace_callback($pattern,function($matches){
			if($matches[1]){
				$output = '<img src="' . theme_features::get_theme_images_url('modules/theme-comment-face/' .$matches[1]) . '" alt="' . ___('Face') . '"/>';
				return $output;
			}
		},$comment_text);
		return $comment_text;
	}
	public static function frontend_seajs_use(){
		if(!is_singular()) return false;
		
		
		$faces_cache = theme_cache::get(self::$iden);
		
		
		if(empty($faces_cache)){
			$face_dirs = theme_features::get_theme_path(theme_features::$basedir_images_min . 'modules/theme-comment-face/');
			$files = glob($face_dirs . '*');
			$faces_cache = array();
			foreach($files as $file){
				$faces_cache[] = basename($file);
			}
			theme_cache::set(self::$iden,$faces_cache);
		}
		?>
		seajs.use(['<?php echo esc_js(theme_features::get_theme_includes_js(__FILE__));?>'],function(m){
			m.config.faces_url = '<?php echo esc_js(theme_features::get_theme_images_url('modules/theme-comment-face/'));?>';
			m.config.faces = <?php echo json_encode($faces_cache);?>;
			m.init();
		});
		<?php
	}
}
?>