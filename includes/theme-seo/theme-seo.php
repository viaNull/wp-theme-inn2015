<?php
/*
Feature Name:	SEO PLUS
Feature URI:	http://www.inn-studio.com
Version:		1.1.3
Description:	Improve the seo friendly
Author:			INN STUDIO
Author URI:		http://www.inn-studio.com
*/
add_action('base_settings','theme_seo_plus::admin',5);
add_action('wp_head','theme_seo_plus::get_site_keywords',1);
add_action('wp_head','theme_seo_plus::get_site_description',1);
add_filter('theme_options_save','theme_seo_plus::save');
class theme_seo_plus{
	private static $iden = 'theme_seo_plus';
	private static $keywords_split = ',';

	public static function admin(){
		
		$options = theme_options::get_options();
		$seo_plus['description'] = isset($options['seo_plus']['description']) ? $options['seo_plus']['description'] : null; 
		$seo_plus['keywords'] = isset($options['seo_plus']['keywords']) ? $options['seo_plus']['keywords'] : null; 
		
		?>
		<!-- SEO meta -->
		<fieldset>
			<legend><?php echo ___('SEO settings');?></legend>
			<p class="description"><?php echo sprintf(___('Fill in the appropriate keywords, can improve search engine friendliness. Use different key words in English comma (%s) to separate.'),self::$keywords_split);?></p>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="seo_plus_description"><?php echo ___('Site description');?></label></th>
						<td>
							<input id="seo_plus_description" name="seo_plus[description]" class="widefat" type="text" value="<?php echo $seo_plus['description'];?>"/>
							<p class="description"><?php echo esc_html(___('Recommend to control that less than 100 words.'));?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="seo_plus_keywords"><?php echo ___('Site keywords');?></label></th>
						<td>
							<input id="seo_plus_keywords" name="seo_plus[keywords]" class="widefat" type="text" value="<?php echo $seo_plus['keywords'];?>"/>
							<p class="description"><?php echo esc_html(sprintf(___('For example: graphic design%s 3D design ...'),self::$keywords_split));?></p>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	<?php
	}
	
	public static function save($options){
		$options['seo_plus'] = isset($_POST['seo_plus']) ? $_POST['seo_plus'] : null;
		return $options;
	}
	public static function get_site_description($echo = true){
		$descriptions = array();
		/** 
		 * in home page
		 */
		if(is_home()){
			$options = theme_options::get_options();
			if(isset($options['seo_plus']['description']) && !empty($options['seo_plus']['description'])){
				$descriptions[] = apply_filters('meta_description_home',$options['seo_plus']['description']);
			}else{
				$descriptions[] = apply_filters('meta_description_home',get_bloginfo('description'));
			}
		/** 
		 * other page
		 */
		}else{
			if(is_singular()){
				global $post;
				setup_postdata($post);
				// var_dump(get_the_excerpt());
				if(!empty($post->post_excerpt)){
					$descriptions[] = apply_filters('meta_description_singular',$post->post_excerpt);
				}else{
					$descriptions[] = apply_filters('meta_description_singular',mb_substr(strip_tags($post->post_content),0,120));
				}
				wp_reset_postdata();
			}else if(is_category()){
				$category_description = category_description();
				$descriptions[] = apply_filters('meta_description_category',$category_description);
			}else if(is_tag()){
				$tag_description = tag_description();
				$descriptions[] = apply_filters('meta_description_tag',$tag_description);
			}
		
		}
		/**
		 * add a hook
		 */
		$descriptions = array_filter(apply_filters('meta_descriptions',$descriptions));
		if(!empty($descriptions)){
			if($echo !== false){
				echo '<meta name="description" content="' . esc_attr(strip_tags(implode(',',$descriptions))) .'"/>';
			}else{
				return $descriptions;
			}
		}
	}
	/**
	 * get_site_keywords
	 * 
	 * @return string
	 * @example 
	 * @version 1.0.0
	 * @author KM (kmvan.com@gmail.com)
	 * @copyright Copyright (c) 2011-2013 INN STUDIO. (http://www.inn-studio.com)
	 **/
	public static function get_site_keywords(){
		$options = theme_options::get_options();
		$all_tags = array();
		$content = null;
		/** 
		 * post page
		 */
		if(is_singular('post')){
			$posttags = get_the_tags();
			if(!empty($posttags)){
				foreach($posttags as $v) {
					$all_tags[] = $v->name;
				}
			}
		/** 
		 * other page
		 */
		}else if(!is_home()){
			$single_term_title = single_term_title('',false);
			$all_tags[] = apply_filters('meta_keywords_not_home',$single_term_title);
		/** 
		 * load keywords
		 */
		}else if(isset($options['seo_plus']['keywords']) && !empty($options['seo_plus']['keywords'])){
			$theme_kws = explode(self::$keywords_split,trim($options['seo_plus']['keywords']));
			if(!empty($theme_kws)){
				foreach($theme_kws as $v){
					if(!empty($v)) $all_tags[] = trim($v);
				}
			}
		}
		/**
		 * add a hook
		 */
		$all_tags = array_filter(apply_filters('meta_keywords',$all_tags));
		if(!empty($all_tags)){
			echo  '<meta name="keywords" content="' . esc_attr(strip_tags(implode(',',$all_tags))) .'"/>';
		}
	}
}
?>