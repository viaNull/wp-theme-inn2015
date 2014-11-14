<?php
/*
Feature Name:	theme-custom-homebox
Feature URI:	http://www.inn-studio.com
Version:		1.0.0
Description:	
Author:			INN STUDIO
Author URI:		http://www.inn-studio.com
*/
theme_custom_homebox::init();
class theme_custom_homebox{
	private static $iden = 'theme_custom_homebox';

	public static function init(){
		add_filter('theme_options_save',get_class() . '::options_save');
		add_filter('after_backend_tab_init',get_class() . '::after_backend_tab_init');
		add_filter('backend_seajs_alias',get_class() . '::backend_seajs_alias');
		add_action('page_settings',get_class() . '::backend_display');
	}
	public static function frontend_display(){
		$options = theme_options::get_options();

	public static function get_home_box($args = null){
		global $post,$wp_query;
		$defaults = array(
			'option_id' => null,
			'dt_title' => null,
			'orderby' => 'views',
			'posts_per_page' => 10,
			'thumbnail_number' => 10,
			'category__in' => array(),
			'target' => '_blank',
			'classes' => array('grid-25','tablet-grid-33','mobile-grid-50'),
		);
		$r = wp_parse_args($args,$defaults);
		extract($r,EXTR_SKIP);
		
		$options = theme_options::get_options();
		
		$category__in = isset($options['tpl']['cat-id-homebox'][$option_id]) ? array($options['tpl']['cat-id-homebox'][$option_id]) : null;
		if(!$category__in) return false;
		
		$content = null;
		
		$category = get_category($category__in[0]);
		?>
		<dl class="mod main-posts">
			<dt class="tabtitle">
				<span class="grid-parent grid-40 tablet-grid-40 mobile-grid-100">
				
					<a href="<?php echo get_category_link($category->term_id);?>" class="link" <?php echo $target ? 'target="_blank"' : null;?> title="<?php echo esc_attr(sprintf(___('Views more about %s'),$category->name));?>"><span class="icon-play"></span><span class="after-icon"><?php echo esc_html($category->name);?></span> <small class="detail"><?php echo ___('&raquo; detail');?></small></a>
				
				</span>
				<?php
				$keywords = isset($options['tpl']['home-box-keywords'][$option_id]) ? $options['tpl']['home-box-keywords'][$option_id] : null;
				if($keywords){
					?>
					<span class="grid-parent grid-60 tablet-grid-60 mobile-grid-100">
						<span class="keywords">
							<?php foreach($keywords as $kw){ ?>
								<a href="<?php echo esc_url(trim($kw['url']));?>" class="keyword" <?php echo $target ? 'target="_blank"' : null;?> title="<?php echo esc_html(___('Views hot keyword'));?>">
									<span class="icon-fire"></span><span class="after-icon"><?php echo esc_html(trim($kw['tx']));?></span>
								</a>
							<?php } ?>
						</span>
					</span>
					<?php
				}
				?>
			</dt>
			<dd class="tabbody post-lists">
			<?php
			/** 
			 * get query
			 */
			$args = array(
				'order' => $order,
				'posts_per_page' => (int)$posts_per_page,
				'category__in' => $category->term_id,
			);
			$wp_query = theme_functions::get_posts_query($args);
			if(have_posts()){
				while(have_posts()){
					the_post();
					if($wp_query->current_post <= $thumbnail_number - 1){
						theme_functions::archive_img_content(array(
							'classes' => $classes,
						));
					}else{
						theme_functions::archive_tx_content(array(
							'classes' => $classes,
						));
					}
				}
			}else{
			?>
				<div class="post-list no-post grid-100"><?php echo status_tip('info',___('Not data in this category'));?></div>			
			<?php 
			}
			wp_reset_postdata();
			wp_reset_query();
			?>
			</dd>
		</dl>

		<?php
	}
	
	public static function backend_display(){
		$options = theme_options::get_options();
		?>
		<fieldset>
			<legend><?php echo ___('Theme home box settings');?></legend>
			<?php
			$home_boxes = isset($options['homebox']['cat-ids-homebox']) ? $options['homebox']['cat-ids-homebox'] : null;
			if($home_boxes){
				foreach($home_boxes as $k => $v){
					echo self::get_home_box_tpl($k);
				}
			}else{
				echo self::get_home_box_tpl(0);
			}
			?>
			<table class="form-table" id="home-box-control">
				<tbody>
					<tr>
						<th scope="row"><?php echo ___('Home box control');?></th>
						<td>
							<a id="home-box-add" href="javascript:void(0);" class="button-primary" data-tpl="<?php echo esc_attr(self::get_home_box_tpl('%'));?>"><?php echo ___('Add a new home box');?></a>
							<a id="home-box-del" href="javascript:void(0);" class="button"><?php echo esc_html(___('Delete a home box'));?></a>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	<?php
	
	}
	public static function get_home_box_tpl($number = '%'){
		$options = theme_options::get_options();
		$kw = isset($options['homebox']['keywords'][$number]) ? $options['homebox']['keywords'][$number] : null;
		$kw_to_str = function($kw){
			if(!is_array($kw)) return $kw;
			$implode_link = function($line){
				if(!is_array($line)) return $line;
				return implode(' = ',$line);
			};
			return implode(PHP_EOL,array_map($implode_link,$kw));
		};
		$kw = is_array($kw) ? $kw_to_str($kw) : $kw;
		$thumbnail_number = isset($options['homebox']['thumbnail-number'][$number]) ? $options['homebox']['thumbnail-number'][$number] : 10;
		$posts_number = isset($options['homebox']['posts-number'][$number]) ? $options['homebox']['posts-number'][$number] : 10;
		$box_title = isset($options['homebox']['title'][$number]) ? $options['homebox']['title'][$number] : null;
		ob_start();
		?>
<fieldset class="home-box-tpl" id="home-box-tpl-<?php echo $number;?>" data-number="<?php echo $number;?>" data-id="home-box-tpl-<?php echo $number;?>">
	<legend><?php echo ___('Home box');?> - <?php echo $number;?></legend>
	<p class="description"><?php echo esc_html(___('Display different content on homepage.'));?></p>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><?php echo esc_html(___('Box title'));?></th>
				<td>
					<input type="text" name="homebox[title][<?php echo $number;?>]" id="home-box-title-<?php echo $number;?>" placeholder="<?php echo esc_attr(___('Box title'));?>" class="widefat" value="<?php echo esc_attr($box_title);?>">
				</td>
			</tr>
			<tr>
				<th scope="row"><?php echo esc_html(___('Category'));?></th>
				<td>
					<?php echo theme_features::cat_option_list('homebox',$number); ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="home-box-posts-number-<?php echo $number;?>"><?php echo esc_html(___('Show posts number'));?></label></th>
				<td>
					<input 
						id="home-box-posts-number-<?php echo $number;?>" 
						type="number" 
						name="homebox[posts-number][<?php echo $posts_number;?>]" 
						class="short-text" 
						value="<?php echo (int)$thumbnail_number;?>"
					/> <span class="description"><?php echo esc_html(___('Must be common multiple of 5 and 2.'));?></span>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="home-box-thumbnail-number-<?php echo $number;?>"><?php echo esc_html(___('Show thumbnail number'));?></label></th>
				<td>
					<input 
						id="home-box-thumbnail-number-<?php echo $number;?>" 
						type="number" 
						name="homebox[thumbnail-number][<?php echo $number;?>]" 
						class="short-text" 
						value="<?php echo (int)$thumbnail_number;?>"
					/> <span class="description"><?php echo esc_html(___('Must be common multiple of 5 and 2.'));?></span>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php echo esc_html(___('Keywords'));?></th>
				<td>
					<textarea name="homebox[keywords][<?php echo $number;?>]" id="home-box-keywords-<?php echo $number;?>" cols="50" rows="5" placeholder="<?php echo ___('Tag1 = http://www.inn-studio.com');?>" class="widefat"><?php echo esc_attr($kw);?></textarea>
					<p class="description"><?php echo esc_html(___('1 tag 1 line.'));?></p>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	public static function options_save($options){
		$options['homebox'] = isset($_POST['homebox']) ? $_POST['homebox'] : null;
		/** 
		 * home_box
		 */
		if(isset($_POST['homebox']['keywords'])){
			// var_dump($_POST['homebox']['keywords']);
			$split_line = function($str){
				$split_link = function($line_str){
					$line = explode('=',$line_str);
					if(count($line) != 2) return $line_str;
					return array(
						'tx' => trim($line[0]),
						'url' => trim($line[1]),
					);
				};
				return array_map($split_link,explode(PHP_EOL,$str));
			};
			$options['homebox']['keywords'] = array_map($split_line,$_POST['homebox']['keywords']);
		}
		return $options;
	}
	public static function after_backend_tab_init(){
		?>
		seajs.use('theme-home-box',function(_m){
			_m.init();
		});
		<?php
	
	}
	public static function backend_seajs_alias($alias){
		$alias['theme-home-box'] = theme_features::get_theme_includes_js(__FILE__);
		return $alias;
	}
}
