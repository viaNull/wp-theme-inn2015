<?php
/*
Feature Name:	Post Copyright
Feature URI:	http://www.inn-studio.com
Version:		1.0.3
Description:	Your post notes on copyright information, although this is not very rigorous. <br/>将您的文章注上版权信息，虽然这不算得十分严谨。
Author:			INN STUDIO
Author URI:		http://www.inn-studio.com
*/
// add_action('page_settings','theme_post_copyright::admin');
// add_filter('theme_options_default','theme_post_copyright::options_default');
// add_filter('theme_options_save','theme_post_copyright::save');
class theme_post_copyright{
	private static $iden = 'theme_post_copyright';
	public static function admin(){
		
		$options = theme_options::get_options();
		$code = isset($options['post_copyright']['code']) ? stripslashes($options['post_copyright']['code']) : null;
		$is_checked = isset($options['post_copyright']['on']) ? ' checked ' : null;
		?>
		<fieldset>
			<legend><?php echo ___('Post Copyright Settings');?></legend>
			<p class="description">
				<?php echo ___('Posts copyright settings maybe protect your word. Here are some keywords that can be used:');?></p>
			<p class="description">
				<input type="text" class="small-text text-select" value="%post_title_text%" title="<?php echo ___('Post Title text');?>" readonly="true"/>
				<input type="text" class="small-text text-select" value="%post_url%" title="<?php echo ___('Post URL');?>" readonly="true"/>
				<input type="text" class="small-text text-select" value="%blog_name%" title="<?php echo ___('Blog name');?>" readonly="true"/>
				<input type="text" class="small-text text-select" value="%blog_url%" title="<?php echo ___('Blog URL');?>" readonly="true"/>
			</p>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="post_copyright_on"><?php echo ___('Enable or not?');?></label></th>
						<td><input type="checkbox" name="post_copyright[on]" id="post_copyright_on" value="1" <?php echo $is_checked;?> /><label for="post_copyright_on"><?php echo ___('Enable');?></label></td>
					</tr>
					<tr>
						<th scope="row"><?php echo ___('HTML code:');?></th>
						<td>
							<textarea id="post_copyright_code" name="post_copyright[code]" class="regular-text text-code"><?php echo $code;?></textarea>
							<p class="description"><label for="post_copyright_restore">
								<input type="checkbox" id="post_copyright_restore" name="post_copyright[restore]" value="1"/>
								<?php echo ___('Restore the post copyright settings');?></label>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<?php
	}
	/**
	 * options_default
	 * 
	 * 
	 * @return array
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 * 
	 */
	public static function options_default($options){
		
		$options['post_copyright']['code'] = '
<ul class="post_copyright">
	<li>
		' .___('Permanent URL: '). '<a href="%post_url%">%post_url%</a>
	</li>
	<li>
		' .___('Welcome to spread: '). ___('Addition to indicate the original, the article of <a href="%blog_url%">%blog_name%</a> comes from the network, if the infringement, please promptly inform.'). '
	</li>
</ul>';
		return $options;
	}
	/**
	 * save 
	 */
	public static function save($options){
		$options['post_copyright'] = isset($_POST['post_copyright']) ? $_POST['post_copyright'] : null;
		if(isset($_POST['post_copyright']) && isset($_POST['post_copyright']['restore'])){
			unset($options['post_copyright']);
		}
		return $options;
	}
	/**
	 * output
	 */
	public static function display(){
		global $post;
		$options = theme_options::get_options();
		$tpl_keywords = array('%post_title_text%','%post_url%','%blog_name%','%blog_url%');
		$output_keywords = array(get_the_title(),get_permalink(),get_bloginfo('name'),home_url());
		$codes = str_ireplace($tpl_keywords,$output_keywords,$options['post_copyright']['code']);
		return stripslashes($codes);
	}
}
if(!function_exists('get_post_copyright')){
	function get_post_copyright(){
		return post_copyright::display();
	}
}
?>