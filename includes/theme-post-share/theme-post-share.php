<?php
/*
Feature Name:	Post Share
Feature URI:	http://www.inn-studio.com
Version:		1.2.5
Description:	
Author:			INN STUDIO
Author URI:		http://www.inn-studio.com
*/
post_share::init();
class post_share{
	private static $iden = 'post_share';

	public static function init(){
		add_filter('theme_options_default',get_class() . '::options_default');
		add_filter('theme_options_save',get_class() . '::options_save');
		add_action('page_settings',get_class() . '::backend_display');
		if(!self::is_enabled()) return false;
		add_action('wp_head',get_class() . '::frontend_css');
		add_action('frontend_seajs_use',get_class() . '::frontend_js');
	}
	public static function display($args = array()){
		global $post;
		$options = theme_options::get_options();
		$img_url = theme_features::get_thumbnail_src();
		$defaults = array(
			'post_title_text' => esc_attr(get_the_title()),
			'post_url' => esc_url(get_permalink()),
			'blog_name' => esc_attr(get_bloginfo('name')),
			'blog_url' => esc_url(home_url()),
			'img_url' => esc_url($img_url),
			'post_excerpt' => esc_attr(mb_substr(html_compress(strip_tags(get_the_excerpt())),0,120)),
			'post_content' => esc_attr(mb_substr(html_compress(strip_tags(get_the_content())),0,120)),
			'author' => esc_attr(get_the_author_meta('display_name',$post->post_author)),
		);
		$output_keywords = wp_parse_args($args,$defaults);
	
		$tpl_keywords = array(
			'%post_title_text%',
			'%post_url%',
			'%blog_name%',
			'%blog_url%',
			'%img_url%',
			'%post_excerpt%',
			'%post_content%',
			'%author%'
			
		);
		$post_share_code = '<div class="post-share">'.stripslashes(str_ireplace($tpl_keywords,$output_keywords,$options['post_share']['code'])).'</div>';

		return $post_share_code;
	}
	
	public static function backend_display(){

		
		$options = theme_options::get_options();
		$is_checked = self::is_enabled() ? ' checked ' : null;
		?>
		<fieldset>
			<legend><?php echo ___('Posts share settings');?></legend>
			<p class="description">
				<?php echo ___('Share your post to everywhere. Here are some keywords that can be used:');?>
			</p>
			<p class="description">
				<input type="text" class="small-text text-select" value="%post_title_text%" title="<?php echo ___('Post Title text');?>" readonly />
				<input type="text" class="small-text text-select" value="%post_url%" title="<?php echo ___('Post URL');?>" readonly />
				<input type="text" class="small-text text-select" value="%blog_name%" title="<?php echo ___('Blog name');?>" readonly />
				<input type="text" class="small-text text-select" value="%blog_url%" title="<?php echo ___('Blog URL');?>" readonly />
				<input type="text" class="small-text text-select" value="%img_url%" title="<?php echo ___('The first picture of the post.');?>" readonly />
				<input type="text" class="small-text text-select" value="%post_excerpt%" title="<?php echo ___('The excerpt of post.');?>" readonly />
				<input type="text" class="small-text text-select" value="%post_content%" title="<?php echo ___('The content of post.');?>" readonly />
			</p>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="post_share_on"><?php echo ___('Enable or not?');?></label></th>
						<td><input type="checkbox" name="post_share[on]" id="post_share_on" value="1" <?php echo $is_checked;?> /><label for="post_share_on"><?php echo ___('Enable');?></label></td>
					</tr>
					<tr>
						<th scope="row"><?php echo ___('HTML codes');?></th>
						<td><textarea id="post_share_code" name="post_share[code]" class="widefat" cols="30" rows="10"><?php echo stripslashes($options['post_share']['code']);?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_html(___('Restore'));?></th>
						<td>
							<label for="post_share_restore">
								<input type="checkbox" id="post_share_restore" name="post_share[restore]" value="1"/>
								<?php echo ___('Restore the post share settings');?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	<?php
	
	}
	
	public static function options_default($options){
		
		ob_start();
		?>
<div class="bdshare_t bds_tools get-codes-bdshare" data-bdshare="{
	'text':'%post_title_text% by %author% <?php echo ___('-- from %blog_name%');?>',
	'url':'%post_url%',
	'pic':'%img_url%'
}">
	<span class="description"><?php echo esc_html(___('Share to: '));?></span>
	<a class="bds_tsina" data-cmd="tsina" title="<?php echo esc_attr(sprintf(___('Share to %s'),___('Sina Weibo')));?>" href="javascript:void(0);"></a>
	<a class="bds_qzone" data-cmd="qzone" href="javascript:void(0);" title="<?php echo esc_attr(sprintf(___('Share to %s'),___('QQ zone')));?>"></a>
	<a class="bds_baidu" data-cmd="baidu" title="<?php echo esc_attr(sprintf(___('Share to %s'),___('Baidu')));?>" href="javascript:void(0);"></a>
	<a class="bds_more" data-cmd="more" href="javascript:void(0);"></a>
</div>				
<?php
		$content = ob_get_contents();
		ob_end_clean();
		$options['post_share']['on'] = 1;
		$options['post_share']['code'] = $content;


		return $options;
	}
	public static function is_enabled(){
		$options = theme_options::get_options();
		if(isset($options['post_share']['on'])){
			return true;
		}else{
			return false;
		}
	}
	public static function options_save($options){
		$options['post_share'] = isset($_POST['post_share']) ? $_POST['post_share'] : null;
		
		if(isset($_POST['post_share']['restore']) || $_POST['post_share']['code'] == ''){
			unset($options['post_share']);
		}

		return $options;
	}
	public static function frontend_css(){
		?>
		<link rel="stylesheet" href="<?php echo theme_features::get_theme_includes_css(__FILE__);?>">
		<?php
	}
	public static function frontend_js(){
		$options = theme_options::get_options();
		if(!isset($options['post_share']) || strstr($options['post_share']['code'],'bdshare') === false) return false;
		?>
		seajs.use('<?php echo theme_features::get_theme_includes_js(__FILE__);?>',function(m){
			m.config.bdshare_js = '<?php echo esc_url(theme_features::get_theme_includes_js(__FILE__,'bdshare'));?>';
			m.init();
		});
		<?php
	}
}
/* 定义函数 */
if(!function_exists('get_post_share')){
	function get_post_share(){
		return post_share::get_post_share();
		
	}
}
if(!function_exists('the_post_share')){
	function the_post_share(){
		echo post_share::get_post_share();
	}
}
?>