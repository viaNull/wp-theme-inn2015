<?php

/** Theme options */
get_template_part('core/core-options');

/** Theme features */
get_template_part('core/core-features');

/** Theme functions */
get_template_part('core/core-functions');

/** 
 * theme_functions
 */
add_action('after_setup_theme','theme_functions::init');
class theme_functions{
	public static $iden = 'inn2015';
	public static $theme_edition = 1;
	public static $theme_date = '2014-11-06 00:00';
	public static $thumbnail_custom ='thumbnail';
	public static $comment_avatar_size = 60;
	public static $cache_expire = 3600;
	/** 
	 * theme_header_translate
	 */
	private static function theme_header_translate(){
		$headers = array(
			'Name' => ___('INN 2015'),
			'ThemeURI' => ___('http://inn-studio.com/inn2015'),
			'Description' => ___('This is an unique theme, beautiful UI, delicate features, excellent efficiency, good experience, all in one for your blog.'),
			'AuthorURI' => ___('http://inn-studio.com'),
		);
	}
	/** 
	 * init
	 */	
	public static function init(){
		/** 
		 * register menu
		 */
		register_nav_menus(
			array(
				'menu-header' 			=> ___('Header menu'),
				'menu-header-mobile' 	=> ___('Header menu mobile'),
				'menu-tools' 			=> ___('Header menu tools'),
			)
		);	
	
		/** 
		 * admin background setting
		 */
		//add_action('page_settings',get_class() . '::backend_display_page_settings',5);
		add_filter('theme_options_save',get_class() . '::options_save');
		// add_filter('theme_options_default',get_class() . '::options_default');
		/** 
		 * frontend_js
		 */
		add_action('frontend_seajs_use',get_class() . '::frontend_js',1);
		/** 
		 * other
		 */
		add_action('widgets_init',get_class() . '::widget_init');
		//self::widget_init();
		add_filter('use_default_gallery_style','__return_false');
		add_filter('body_class',get_class() . '::body_class');
		//add_filter('prev_pagination_link',get_class() . '::filter_prev_pagination_link',99,3);
		//add_filter('next_pagination_link',get_class() . '::filter_next_pagination_link',99,3);
		add_theme_support('html5', array('search-form'));
		/** 
		 * query_vars
		 */
		//add_filter('query_vars', get_class() . '::filter_query_vars');
		/** 
		 * banner
		 */
		//add_filter('header_banner',get_class() . '::filter_header_banner',1);
		/** 
		 * bg
		 */
		add_theme_support('custom-background',array(
			'default-color'			=> 'eeeeee',
			'default-image'			=> '',
			'default-position-x'	=> 'center',
			'default-attachment'	=> 'fixed',
			'wp-head-callback'		=> 'theme_features::_fix_custom_background_cb',
		));

	}
	
	public static function frontend_js(){
		?>
		seajs.use('frontend',function(m){
			m.init();
		});
		
		<?php
		/** 
		 * post toc
		 */
		if(is_singular()){
			?>
			seajs.use('modules/jquery.posttoc',function(m){
				m.config.lang.M00001 = '<?php echo  ___('Post Toc');?>';
				m.config.lang.M00002 = '<?php echo  ___('[Top]');?>';
				m.init();
			});
			<?php
		}
	}
	/** 
	 * widget_init
	 */
	public static function widget_init(){
		$sidebar = array(
			array(
				'name' 			=> ___('Home widget area'),
				'id'			=> 'widget-area-home',
				'description' 	=> ___('Appears on home in the sidebar.')
			),
			array(
				'name' 			=> ___('Archive page widget area'),
				'id'			=> 'widget-area-archive',
				'description' 	=> ___('Appears on archive page in the sidebar.')
			),
			array(
				'name' 			=> ___('Footer widget area'),
				'id'			=> 'widget-area-footer',
				'description' 	=> ___('Appears on all page in the footer.'),
				'before_widget' => '<div class="grid-25 tablet-grid-25 mobile-grid-100"><aside id="%1$s"><div class="widget %2$s">',
				'after_widget'		=> '</div></aside></div>',
			),
			array(
				'name' 			=> ___('Singular post widget area'),
				'id'			=> 'widget-area-post',
				'description' 	=> ___('Appears on post in the sidebar.')
			),
			array(
				'name' 			=> ___('Singular page widget area'),
				'id'			=> 'widget-area-page',
				'description' 	=> ___('Appears on page in the sidebar.')
			),
			array(
				'name' 			=> ___('Sign page widget area'),
				'id'			=> 'widget-area-sign',
				'description' 	=> ___('Appears on sign page in the sidebar.')
			),
			array(
				'name' 			=> ___('404 page widget area'),
				'id'			=> 'widget-area-404',
				'description' 	=> ___('Appears on 404 no found page in the sidebar.')
			)
		);
		foreach($sidebar as $v){
			register_sidebar(array(
				'name'				=> $v['name'],
				'id'				=> $v['id'],
				'description'		=> $v['description'],
				'before_widget'		=> isset($v['before_widget']) ? $v['before_widget'] : '<aside id="%1$s"><div class="widget %2$s">',
				'after_widget'		=> isset($v['after_widget']) ? $v['after_widget'] : '</div></aside>',
				'before_title'		=> isset($v['before_title']) ? $v['before_title'] : '<h3 class="widget-title">',
				'after_title'		=> isset($v['after_title']) ? $v['after_widget'] : '</h3>',
			));
		}
	}


	/** 
	 * options_save
	 */
	public static function options_save($options){
		if(isset($_POST['tpl'])){
			$options['tpl'] = $_POST['tpl'];
		}
		return $options;
	}
	/** 
	 * in_category
	 */
	public static function in_category($options_cat_id){
		if(is_category() || is_single()){
			$options = theme_options::get_options();
			$current_cat_id = theme_features::get_current_cat_id();
			
			if(!isset($options['tpl']) || !isset($options['tpl'][$options_cat_id])) return false;
			$works_cat_ids = $options['tpl'][$options_cat_id];
			return in_array($current_cat_id,$works_cat_ids);
		}		
	}

	public static function body_class($classes){
		return $classes;
	}
	public static function filter_query_vars($vars){
		if(!in_array('paged',$vars)) $vars[] = 'paged';
		if(!in_array('tab',$vars)) $vars[] = 'tab';
		// if(!in_array('orderby',$vars)) $vars[] = 'orderby'; /** = type */
		return $vars;
	}
	public static function filter_header_banner($str){
		if(is_home()) return $str;
		$str = theme_features::get_wp_title();
		return $str;
	}
	/**
	 * tab type
	 *
	 * @param string
	 * @return array|string|false
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 */
	public static function get_tab_type($key = null){
		$typies = array(
			'lastest' => array(
				'icon' => 'gauge',
				'text' => ___('Lastest')
			),
			'pop' => array(
				'icon' => 'happy',
				'text' => ___('Popular')
			),
			'rand' => array(
				'icon' => 'shuffle',
				'text' => ___('Random')
			),
		);
		if($key){
			return isset($typies[$key]) ? $typies[$key] : false;
		}else{
			return $typies;
		}
	}
	public static function the_neck($args = null){
		
	}
	/**
	 * Output orderby nav in Neck position
	 *
	 * @return 
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 */
	public static function the_order_nav($args = null){
		$current_tab = get_query_var('tab');
		$current_tab = !empty($current_tab) ? $current_tab : 'lastest';
		$typies = self::get_tab_type();
		if(is_home()){
			$current_url = home_url();
		}else if(is_category()){
			$cat_id = theme_features::get_current_cat_id();
			$current_url = get_category_link($cat_id);
		}else if(is_tag()){
			$tag_id = theme_features::get_current_tag_id();
			$current_url = get_tag_link($tag_id);
		}else{
			$current_url = get_current_url();
		}
		?>
		<nav class="page-nav">
			<?php
			foreach($typies as $k => $v){
				$current_class = $current_tab === $k ? 'current' : null;
				$url = add_query_arg('tab',$k,$current_url);
				?>
				<a href="<?php echo esc_url($url);?>" class="item <?php echo $current_class;?>">
					<span class="icon-<?php echo $v['icon'];?>"></span><span class="after-icon"><?php echo esc_html($v['text']);?></span>
				</a>
				<?php
			}
			?>
		</nav>
		<?php
	}
	public static function get_home_posts($args = null){
		global $post,$wp_query;
		$options = theme_options::get_options();
		$home_data_filter = isset($options['home-data-filter']) ? $options['home-data-filter'] : null;
		$defaults = array(
			'date' => $home_data_filter,
			'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
			'current_tab' => get_query_var('tab') ? get_query_var('tab') : 'lastest',
			'posts_per_page' => get_option('posts_per_page'),
		);
		$r = wp_parse_args($args,$defaults);
		extract($r);
		
		$query_args['paged'] = $paged;
		$query_args['date'] = $date;
		$query_args['posts_per_page'] = $posts_per_page;

		switch($current_tab){
			case 'pop':
				$query_args['orderby'] = 'thumb-up';
				break;
			case 'rand':
				$query_args['orderby'] = 'rand';
				break;
			default:
				$query_args['orderby'] = 'lastest';
				$query_args['date'] = 'all';
		}
		$wp_query = self::get_posts_query($query_args);
		
		return $wp_query;
	}
	public static function get_posts_query($args){
		global $paged;
		$options = theme_options::get_options();
		$defaults = array(
			'orderby' => 'views',
			'order' => 'desc',
			'posts_per_page' => get_option('posts_per_page'),
			'paged' => 1,
			'category__in' => isset($options['tpl']['cat-ids-works']) ? $options['tpl']['cat-ids-works'] : array(),
			'date' => 'all',
			
		);
		$r = wp_parse_args($args,$defaults);
		extract($r);
		$query_args = array(
			'posts_per_page' => $posts_per_page,
			'paged' => $paged,
			'ignore_sticky_posts' => 1,
			'category__in' => $category__in,
			'post_status' => 'publish',
			'post_type' => 'post',
			'has_password' => false,
			
		);
		
		switch($orderby){
			case 'views':
				$query_args['meta_key'] = 'views';
				$query_args['orderby'] = 'meta_value_num';
				break;
			case 'thumb-up':
			case 'thumb':
				$query_args['meta_key'] = 'post_thumb_count_up';
				$query_args['orderby'] = 'meta_value_num';
				break;
			case 'rand':
			case 'random':
				$query_args['orderby'] = 'rand';
				break;
			case 'latest':
				$query_args['orderby'] = 'date';
				break;
			case 'comment':
				$query_args['orderby'] = 'comment_count';
				break;
			case 'sticky':
				$query_args['post__in'] = get_option( 'sticky_posts' );
				unset($query_args['ignore_sticky_posts']);
				unset($query_args['post__not_in']);
				break;
			default:
				$query_args['orderby'] = 'date';
		}
		if(!$date || $date != 'all'){
			/** 
			 * date query
			 */
			switch($date){
				case 'daily' :
					$after = 'day';
					break;
				case 'weekly' :
					$after = 'week';
					break;
				case 'monthly' :
					$after = 'month';
					break;
				default:
					$after = 'day';
					break;
			}
			$query_args['date_query'] = array(
				array(
					'column' => 'post_date_gmt',
					'after'  => '1 ' . $after . ' ago',
				)
			);
		}
		return theme_cache::get_queries($query_args);
	}
	/**
	 * archive_img_content
	 *
	 * @return
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 */
	public static function archive_img_content($args = array()){
		$defaults = array(
			'classes' => array('grid-20','tablet-grid-20','mobile-grid-50'),
			'lazyload' => true,
			'target' => '_blank',
		);
		$r = wp_parse_args($args,$defaults);
		extract($r,EXTR_SKIP);

		global $post;
		$classes[] = 'post-list post-img-list';
		$post_title = get_the_title();
		$target = $target ? ' target="' . $target . '" ' : null;

		$excerpt = get_the_excerpt() ? ' - ' . get_the_excerpt() : null;

		$thumbnail_real_src = theme_functions::get_thumbnail_src($post->ID,self::$thumbnail_custom);
		?>
		<section class="<?php echo esc_attr(implode(' ',$classes));?>">
			<a class="post-list-bg" href="<?php echo get_permalink();?>">
				<img class="post-list-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" data-original="<?php echo esc_url($thumbnail_real_src);?>" alt="<?php echo esc_attr($post_title);?>" width="150" height="150"/>
			<h3 class="post-list-title" title="<?php echo esc_attr($post_title),esc_attr($excerpt);?>"><?php echo esc_html($post_title);?></h3>
			</a>
		</section>
		<?php
	}
	/**
	 * get_meta_type
	 *
	 * @param string $type
	 * @return array
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 */
	public static function get_meta_type($type){
		global $post;
		$output = array();
		switch($type){
			case 'thumb-up':
				$output = array(
					'icon' => 'thumbs-o-up',
					'num' => (int)get_post_meta($post->ID,'post_thumb_count_up',true),
					'tx' => ___('Thumb up'),
				);
				break;
			case 'comments':
				$output = array(
					'icon' => 'bubble',
					'num' => $post->comment_count,
					'tx' => ___('Comment count'),
				);
				break;
			default:
				$output = array(
					'icon' => 'play',
					'num' => (int)get_post_meta($post->ID,'views',true),
					'tx' => ___('Views'),
				);
				break;
		}
		return $output;
	}
	public static function archive_tx_content($args = array()){
		global $post;
		$defaults = array(
			'target' 			=> '_blank',
			'classes'			=> array(),
			'meta_type'			=> 'views',
		);
		$r = wp_parse_args($args,$defaults);
		extract($r,EXTR_SKIP);
		
		$post_title = get_the_title();
		$target = $target ? ' target="' . $target . '" ' : null;
		/** 
		 * classes
		 */
		$classes[] = 'post-list post-tx-list';
		$classes = implode(' ',$classes);
		
		$meta_type = self::get_meta_type($meta_type);
		?>
		<section class="<?php echo esc_attr($classes);?>">
			<a href="<?php echo esc_url(get_permalink());?>" title="<?php echo esc_attr($post_title);?>">
				<?php
				if($meta_type){
					?>
					<span class="post-list-meta" title="<?php echo esc_attr($meta_type['tx']);?>">
						<span class="icon-<?php echo $meta_type['icon'];?>"></span><span class="after-icon"><?php echo $meta_type['num'];?></span>
					</span>
					<?php
				}
				?>
				<span class="tx"><?php echo esc_html($post_title);?></span>
			</a>
		</section>
		<?php
		
	}
	
	/** 
	 * archive_content
	 */
	public static function archive_content($args = array()){
		global $post;
		
		$defaults = array(
			'target' 			=> '_blank',
			'classes'			=> array('grid-50','tablet-grid-50','mobile-grid-100'),
			'show_author' 		=> true,
			'show_date' 		=> true,
			'show_views' 		=> true,
			'show_comms' 		=> true,
			'show_rating' 		=> true,
			'lazyload'			=> true,
			
		);
		$r = wp_parse_args($args,$defaults);
		extract($r,EXTR_SKIP);
		
		global $post;
		$classes[] = 'post-list post-mixed-list';
		$post_title = get_the_title();
		$target = $target ? ' target="' . $target . '" ' : null;

		$excerpt = get_the_excerpt() ? get_the_excerpt() : null;
		/** 
		 * classes
		 */
		
		/** 
		 * cache author datas
		 */
		$author = get_user_by('id',$post->post_author);
		$thumbnail_real_src = theme_functions::get_thumbnail_src($post->ID,self::$thumbnail_custom);
		
		?>
		<section class="<?php echo esc_attr(implode(' ',$classes));?>">
			<a class="post-list-bg" href="<?php echo get_permalink();?>">
				<img class="post-list-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" data-original="<?php echo esc_url($thumbnail_real_src);?>" alt="<?php echo esc_attr($post_title);?>" width="150" height="150"/>
				<div class="area-tx">
					<h3 class="post-list-title" title="<?php echo esc_attr($post_title);?>"><?php echo esc_html($post_title);?></h3>
					<p class="excerpt" title="<?php echo esc_attr($excerpt);?>"><?php echo esc_html($excerpt);?></p>		
					<div class="exten">
						<div class="item"></div>
					</div>
				</div>
			</a>
		</section>
		<?php
	}
	public static function page_content($args = array()){
		global $post;
		
		$defaults = array(
			'target' 			=> '_blank',
			'classes'			=> array('grid-100','tablet-grid-100','mobile-grid-100'),
			'show_author' 		=> true,
			'show_date' 		=> true,
			'show_views' 		=> true,
			'show_comms' 		=> true,
			'show_rating' 		=> true,
			'lazyload'			=> true,
			
		);
		$r = wp_parse_args($args,$defaults);
		extract($r,EXTR_SKIP);
		
		$post_title = get_the_title();
		$target = $target ? ' target="' . $target . '" ' : null;
		/** 
		 * classes
		 */
		$classes[] = 'singluar-post';
		/** 
		 * cache author datas
		 */
		$author = get_user_by('id',$post->post_author);
		
		?>
		<article id="post-<?php the_ID();?>" <?php post_class($classes);?>>
			<?php if(!empty($post_title)){ ?>
				<h3 class="entry-title"><?php echo esc_html($post_title);?></h3>
			<?php } ?>
			<!-- post-content -->
			<div class="post-content content-reset">
				<?php the_content();?>
			</div>
			<?php// self::the_post_pagination();?>
		</article>
		<?php
	}
	/** 
	 * singular_content
	 */
	public static function singular_content($args = array()){
		global $post;
		
		$defaults = array(
			'target' 			=> '_blank',
			'classes'			=> array('grid-100','tablet-grid-100','mobile-grid-100'),
			'show_author' 		=> true,
			'show_date' 		=> true,
			'show_views' 		=> true,
			'show_comms' 		=> true,
			'show_rating' 		=> true,
			'lazyload'			=> true,
			
		);
		$r = wp_parse_args($args,$defaults);
		extract($r,EXTR_SKIP);
		
		$post_title = get_the_title();
		$target = $target ? ' target="' . $target . '" ' : null;
		/** 
		 * classes
		 */
		$classes[] = 'singluar-post';
		/** 
		 * cache author datas
		 */
		$author = get_user_by('id',$post->post_author);
		
		?>
		<article id="post-<?php the_ID();?>" <?php post_class($classes);?>>
			<?php if(!empty($post_title)){ ?>
				<h3 class="entry-title"><?php echo esc_html($post_title);?></h3>
			<?php } ?>
			<!-- author avatar -->
			<header class="post-header post-metas">
				<!-- category -->
				<?php
				$cats = get_the_category_list(', ');
				if(!empty($cats)){
					?>
					<span class="post-meta post-category" title="<?php echo esc_attr(___('Category'));?>">
						<span class="icon-folder"></span><span class="after-icon"><?php echo $cats;?></span>
					</span>
				<?php } ?>
				<!-- time -->
				<time class="post-meta post-time" datetime="<?php echo esc_attr(get_the_time('Y-m-d H:i:s'));?>">
					<span class="icon-clock"></span><span class="after-icon"><?php echo esc_html(friendly_date((get_the_time('U'))));?></span>
				</time>
				<!-- author link -->
				<a class="post-meta post-author" href="<?php echo get_author_posts_url($author->ID);?>" title="<?php echo esc_attr(sprintf(___('Views all post by %s'),$author->display_name));?>">
					<span class="icon-user"></span><span class="after-icon"><?php echo esc_html($author->display_name);?></span>
				</a>
				<!-- views -->
				<?php if(class_exists('theme_post_views') && theme_post_views::is_enabled()){ ?>
					<span class="post-meta post-views" title="<?php echo esc_attr(___('Views'));?>">
						<span class="icon-play"></span><span class="after-icon"><?php echo esc_html(theme_post_views::display());?></span>
					</span>
				<?php } ?>
				<!-- permalink -->
				<a href="<?php echo get_permalink();?>" class="post-meta permalink" title="<?php echo esc_attr(___('Post link'));?>">
					<span class="icon-link"></span><span class="after-icon"><?php echo esc_html(___('Post link'));?></span>
				</a>
			</header>
			<!-- post-content -->
			<div class="post-content content-reset">
				<?php the_content();?>
			</div>
			<?php echo theme_features::get_prev_next_pagination(array(
				'numbers_class' => array('btn btn-primary')
			));?>
			<?php
			/** 
			 * tags
			 */
			// self::the_post_tags();
			$tags = get_the_tags();
			if(!empty($tags)){
				?>
				<div class="post-tags">
				<?php
				foreach($tags as $tag){
					?>
					<a href="<?php echo get_tag_link($tag->term_id);?>" class="post-meta tag" title="<?php echo sprintf(___('Views all posts by %s tag'),esc_attr($tag->name));?>">
						<span class="icon-tag"></span><span class="after-icon"><?php echo esc_html($tag->name);?></span>
					</a>
					<?php
				}
				?>
				</div>
				<?php
			}
			?>
			<!-- post-footer -->
			<footer class="post-footer post-metas">
				
				<?php
				/** 
				 * thumb-up
				 */
				if(class_exists('theme_post_thumb') && theme_post_thumb::is_enabled()){
					?>
					<div class="post-thumb">
						<a data-post-thumb="<?php echo $post->ID;?>,up" href="javascript:void(0);" class="post-meta theme-thumb theme-thumb-up" title="<?php echo ___('Good! I like it.');?>">
							<span class="icon-thumbs-o-up"></span><span class="after-icon count"><?php echo theme_post_thumb::get_thumb_up_count();?></span>
							 <span class="tx hide-on-mobile"><?php echo ___('Good');?></span>
						</a>
						<a data-post-thumb="<?php echo $post->ID;?>,down" href="javascript:void(0);" class="post-meta theme-thumb theme-thumb-down" title="<?php echo ___('Bad idea!');?>">
							<span class="icon-thumbs-o-down"></span><span class="after-icon count"><?php echo theme_post_thumb::get_thumb_down_count();?></span>
							<span class="tx hide-on-mobile"><?php echo ___('Bad');?></span>
						</a>
					</div>
				
				<?php } /** end thumb-up */ ?>
				
				<?php
				/** 
				 * post-share
				 */
				if(class_exists('post_share') && post_share::is_enabled()){
					?>
					<div class="post-meta">
						<?php echo post_share::display();?>
					</div>
					<?php
				} /** end post-share */
				?>
				<?php
				/** 
				 * comment
				 */
				$comment_count = (int)get_comments_number();
				$comment_tx = $comment_count <= 1 ? ___('comment') : ___('comments');
				?>
				<a href="javascript:void(0);" class="post-meta quick-comment comment-count" data-post-id="<?php echo $post->ID;?>">
					<span class="icon-bubble"></span><span class="after-icon"><span class="comment-count-number"><?php echo esc_html($comment_count);?></span> <span class="hide-on-mobile"><?php echo esc_html($comment_tx);?></span></span>
				</a>
				
			</footer>
		</article>
		<?php
	}
	
	public static function the_post_tags(){
		global $post;
		$tags = get_the_tags();
		if(empty($tags)) return false;
		$first_tag = array_shift($tags);
		$split_str = '<span class="split">' . ___(', ') . '</span>';
		?>
		<div class="post-tags">
			<?php
			/** 
			 * first tag html
			 */
			ob_start();
			?>
			<a href="<?php echo get_tag_link($first_tag->term_id);?>" class="tag" title="<?php echo sprintf(___('Views all posts by %s tag'),esc_attr($first_tag->name));?>">
				<span class="icon-tags"></span><span class="after-icon"><?php echo esc_html($first_tag->name);?></span>
			</a>
			<?php
			$tags_str = array(ob_get_contents());
			ob_end_clean();
			// $i = 0;
			foreach($tags as $tag){
				// if($i === 0){
					// ++$i;
					// continue;
				// }
				ob_start();
				?>
				<a href="<?php echo get_tag_link($tag->term_id);?>" class="tag" title="<?php echo sprintf(___('Views all posts by %s tag'),esc_attr($tag->name));?>">
					<?php echo esc_html($tag->name);?>
				</a>
				<?php
				$tags_str[] = ob_get_contents();
				ob_end_clean();
			} 
			echo implode($split_str,$tags_str);
			?>
			
		</div>
		<?php
	}
	/**
	 * get_thumbnail_src
	 *
	 * @return 
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 */
	public static function get_thumbnail_src($post_id = null,$size = null){
		global $post;

		$size = $size ? $size : self::$thumbnail_custom;
		$post_id = $post_id ? $post_id : $post->ID;
		$options = theme_options::get_options();
		$custom_features_meta = null;
		$src = null;
		/**
		 * get the options from theme custom_features_meta
		 */
		if(isset($options['custom_features_meta']) && !empty($options['custom_features_meta'])){
			$custom_features_meta = $options['custom_features_meta'];
		/**
		 * get the options from plugin
		 */
		}else if(class_exists('plugin_options_sinapicv2')){
			$plugin_options = plugin_options_sinapicv2::get_options();
			if(isset($plugin_options['sinapic']['feature_meta'])){
				$custom_features_meta = isset($plugin_options['sinapic']['feature_meta']) ? $plugin_options['sinapic']['feature_meta'] : null; 
			}else{
				$custom_features_meta = isset($plugin_options['feature_meta']) ? $plugin_options['feature_meta'] : null;
			}
		}
		$src = $custom_features_meta ? get_post_meta($post_id,$custom_features_meta,true) : null;
		// var_dump($custom_features_meta);
		if(empty($src)){
			$src = get_img_source(get_the_post_thumbnail($post_id,$size));
		}
		if(!$src){
			$src = theme_features::get_theme_images_url('frontend/thumb-preview.png');
		}
		
		return $src;
	}
	/**
	 * get_content
	 *
	 * @return string
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 */
	private static function get_content(){
		global $post;
		$content = str_replace(']]>', ']]&raquo;', $post->post_content);				
		return $content;
	}

 	/**
	 * get_adjacent_posts
	 *
	 * @param string
	 * @return string
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 */
	public static function get_adjacent_posts($class = 'adjacent-posts'){
		global $post;
		$next_post = get_adjacent_post(true,null,false);
		$next_post = $next_post ? $next_post : get_adjacent_post(false,null,false);
		
		
		$prev_post = get_adjacent_post(true,null);
		$prev_post = $prev_post ? $prev_post : get_adjacent_post(false,null);
		
		if(!$next_post && ! $prev_post) return;
		
		ob_start();
		?>
		<nav class="grid-100 grid-parent <?php echo $class;?>">
			<ul>
				<li class="adjacent-post-prev grid-50 tablet-grid-50 mobile-grid-100">
					<?php if(!$prev_post){ ?>
						<span class="adjacent-post-not-found button"><?php echo ___('No more post found');?></span>
					<?php }else{ ?>
						<a href="<?php echo get_permalink($prev_post->ID);?>" title="<?php echo esc_attr(sprintf(___('Previous post: %s'),$prev_post->post_title));?>" class="button">
							<span class="aquo"><?php echo esc_html(___('&laquo;'));?></span>
							<?php echo esc_html($prev_post->post_title);?>
						</a>
					<?php } ?>
				</li>
				<li class="adjacent-post-next grid-50 tablet-grid-50 mobile-grid-100">
					<?php if(!$next_post){ ?>
						<span class="adjacent-post-not-found button"><?php echo ___('No more post found');?></span>
					<?php }else{ ?>
						<a href="<?php echo get_permalink($next_post->ID);?>" title="<?php echo esc_attr(sprintf(___('Next post: %s'),$next_post->post_title));?>"  class="button">
							<?php echo esc_html($next_post->post_title);?>
							<span class="aquo"><?php echo esc_html(___('&raquo;'));?></span>
						</a>
					<?php } ?>
				</li>
			</ul>
		</nav>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;

	}
   /**
     * get_crumb
     * 
     * 
     * @return string The html code
     * @version 2.0.3
     * @author KM@INN STUDIO
     * 
     */
    public static function get_crumb($args = null){
		$defaults = array(
			'header' => null,
			'footer' => null,
		);
		$r = wp_parse_args($args,$defaults);
		extract($r,EXTR_SKIP);
		
		
		$links = array();
    	if(is_home()) return null;
		
		$links['home'] = '<a href="' . home_url() . '" class="home" title="' .___('Back to Homepage'). '"><span class="icon-home"></span><span class="after-icon hide-on-mobile">' . esc_html(___('Home')) . '</span></a>';
		$split = '<span class="split">&raquo;</span>';
		
    	/* category */
    	if(is_category()){
			$cat_curr = theme_features::get_current_cat_id();
			if($cat_curr > 1){
				$links_cat = get_category_parents($cat_curr,true,'%split%');
				$links_cats = explode('%split%',$links_cat);
				array_pop($links_cats);
				$links['category'] = implode($split,$links_cats);
				$links['curr_text'] = esc_html(___('Category Browser'));
			}
    	/* tag */
    	}else if(is_tag()){
    		$tag_id = theme_features::get_current_tag_id();
			$tag_obj = get_tag($tag_id);
    		$links['tag'] = '<a href="'. esc_url(get_tag_link($tag_id)).'">' . esc_html(theme_features::get_current_tag_name()).'</a>';
    		$links['curr_text'] = esc_html(___('Tags Browser'));
    		/* date */
    	}else if(is_date()){
    		global $wp_query;
    		$day = $wp_query->query_vars['day'];
    		$month = $wp_query->query_vars['monthnum'];
    		$year = $wp_query->query_vars['year'];
    		/* day */
    		if(is_day()){
    			$date_link = get_day_link(null,null,$day);
    		/* month */
    		}else if(is_month()){
    			$date_link = get_month_link($year,$month);
    		/* year */
    		}else if(is_year()){
    			$date_link = get_year_link($year);
    		}
    		$links['date'] = '<a href="'.$date_link.'">' . esc_html(wp_title('',false)).'</a>';
    		$links['curr_text'] = esc_html(___('Date Browser'));
    	/* search*/
    	}else if(is_search()){
    		// $nav_link = null;
    		$links['curr_text'] = esc_html(sprintf(___('Search Result: %s'),get_search_query()));
    	/* archive */
    	}else if(is_archive()){
    		$links['archive'] = '<a href="'.get_current_url().'">'.wp_title('',false).'</a>';
    		$links['curr_text'] = esc_html(___('Archive Browser'));
    	/* Singular */
    	}else if(is_singular()){
			global $post;
			/* The page parent */
			if($post->post_parent){
				$links['singluar'] = '<a href="' .get_page_link($post->post_parent). '">' .esc_html(get_the_title($post->post_parent)). '</a>';
			}
			/**
			 * post / page
			 */
    		if(theme_features::get_current_cat_id() > 1){
				$categories = get_the_category();
				foreach ($categories as $key => $row) {
							$parent_id[$key] = $row->category_parent;
				}
				array_multisort($parent_id, SORT_ASC,$categories);
				foreach($categories as $cat){
					$links['singluar'] = '<a href="' . esc_html(get_category_link($cat->cat_ID)) . '" title="' . esc_attr(sprintf(___('View all posts in %s'),$cat->name)) . '">' . esc_html($cat->name) . '</a>';
				}
    		}
    		$links['curr_text'] = esc_html(get_the_title());
    	/* 404 */
    	}else if(is_404()){
    		// $nav_link = null;
    		$links['curr_text'] = esc_html(___('Not found'));
    	}
	
    $output = '
		<div class="crumb-container">
			' .$header. '
			<nav class="crumb">
				' . implode($split,apply_filters('crumb_home_link',$links)) . '
			</nav>
			' .$footer. '
		</div>
		';
		return $output;
    }
	/**
	 * get_post_pagination
	 * show pagination in archive or searching page
	 * 
	 * @param string The class of molude
	 * @return string
	 * @version 1.0.1
	 * @author KM@INN STUDIO
	 * 
	 */
	public static function get_post_pagination( $class = 'posts-pagination' ) {
		global $wp_query,$paged;
		if ( $wp_query->max_num_pages > 1 ){
			$big = 9999999;
			$args = array(
				'base'			=> str_replace( $big, '%#%', get_pagenum_link( $big ) ),
				'echo'			=> false, 
				'current' 		=> max( 1, get_query_var('paged') ),
				'prev_text'		=> ___('&laquo;'),
				'next_text'		=> ___('&raquo;'),
				'total'			=> $wp_query->max_num_pages,
			);
			$posts_page_links = paginate_links($args);
			
			$output = '<nav class="'.$class.'">'.$posts_page_links.'</nav>';
			return $output;
		}
	}
	
	public static function get_theme_respond($args = null){
		global $post,$current_user;
		$defaults = array(
			'post_id' => $post->ID ? $post->ID : null,
			'parent_id' => 0,
		);
		$r = wp_parse_args($args,$defaults);
		extract($r);
		$current_commenter = wp_get_current_commenter();
		// $comment_author = isset($current_commenter['comment_author']) && !empty($$current_commenter['comment_author']) ? $current_commenter['comment_author'] : 
		get_currentuserinfo();
		
		ob_start();
		
		?>
		<div id="respond" class="comment-respond">
			<h3 id="reply-title" class="comment-reply-title">
				<span class="icon-bubble"></span><span class="after-icon"><?php echo esc_html(___('Leave a comment'));?></span>
				<small><a rel="nofollow" id="cancel-comment-reply-link" href="javascript:void(0);" style="display:none;"><span class="icon-cancel-circle"></span><span class="after-icon"><?php echo esc_html(___('Cancel reply'));?></span></a></small>
			</h3>
			<form action="javascript:void(0);" method="post" id="commentform" class="comment-form">
				<div class="area-user">
					<?php
					if(!is_user_logged_in()){
						if(empty($current_commenter['comment_author'])){
							?>
							<p><input type="text" name="author" id="comment-author" class="form-control mod" placeholder="<?php echo esc_attr(___('Name'));?>"/></p>
							<p><input type="email" name="email" id="comment-email" class="form-control mod" placeholder="<?php echo esc_attr(___('Email'));?>"/></p>
							
							<?php
						}else{
							?>
							<a href="<?php echo !empty($current_commenter['comment_author_url']) ? $current_commenter['comment_author_url'] : 'javascript:void(0);';?>" class="area-avatar" <?php echo !empty($current_commenter['comment_author_url']) ? 'target="_blank"' : null;?>">
								<img src="<?php echo esc_url(!empty($current_commenter['comment_author_email']) ? get_gravatar($current_commenter['comment_author_email']) : theme_features::get_theme_images_url('frontend/author-vcard.jpg'));?>" title="<?php echo esc_attr($current_commenter['comment_author']);?>" alt="<?php echo esc_attr($current_commenter['comment_author']);?>"/>
							</a>
						<?php } ?>
					<?php }else{ ?>
						<a href="<?php echo !empty($current_user->user_url) ? $current_user->user_url : 'javascript:void(0);';?>" class="area-avatar" <?php echo !empty($current_user->user_url) ? 'target="_blank"' : null;?>">
							<img src="<?php echo esc_url(!empty($current_user->user_email) ? get_gravatar($current_user->user_email) : theme_features::get_theme_images_url('frontend/author-vcard.jpg'));?>" title="<?php echo esc_attr($current_user->display_name);?>" alt="<?php echo esc_attr($current_user->display_name);?>"/>
						</a>
					<?php } ?>
				</div>
				<div class="area-comment mod">
					<textarea id="comment" name="comment" cols="45" rows="8" required placeholder="<?php echo esc_html(___('Write a omment'));?>" class="form-control"></textarea>
					
					<!-- #comment face system -->
					<?php
					$options = theme_options::get_options();
					$emoticons = theme_comment_face::get_emoticons();
					$a_content = null;
					if($emoticons){
						foreach($emoticons as $text){
							$a_content .= '<a href="javascript:void(0);" data-id="' . esc_attr($text) . '">' . esc_html($text) . '</a>';
						}
					}else{
						$a_content = '<a href="javascript:void(0);" data-id="' . esc_html(___('No data')) . '">' . esc_html(___('No data')) . '</a>';
					}
					?>
					<div id="comment-face" class="hide-no-js">
						<ul class="comment-face-btns grid-parent grid-40 tablet-grid-40 mobile-grid-30">
							<li class="btn grid-parent grid-50 tablet-grid-50 mobile-grid-50" data-faces="">
								<a title="<?php echo esc_attr(___('Pic-face'));?>" href="javascript:void(0);" class="comment-face-btn">
									<span class="icon-happy"></span><span class="after-icon hide-on-mobile"><?php echo esc_html(___('Pic-face'));?></span>
								</a>
								<div class="comment-face-box type-image"></div>
							</li>
							<li class="btn grid-parent grid-50 tablet-grid-50 mobile-grid-50">
								<a title="<?php echo esc_attr(___('Emoticons'));?>" href="javascript:void(0);" class="comment-face-btn">
									<span class="icon-happy2"></span><span class="after-icon hide-on-mobile"><?php echo esc_html(___('Emoticons'));?></span>
								</a>
								<div class="comment-face-box type-text"><?php echo $a_content;?></div>
							</li>
						</ul>
						<!-- submit -->
						<div class="grid-parent grid-60 tablet-grid-60 mobile-grid-70">
							<input class="btn btn-primary" type="submit" id="comment-submit" value="<?php echo esc_html(___('Post comment'));?>">
							<input type="hidden" name="comment_post_ID" value="<?php echo esc_html((int)$post_id);?>" id="comment_post_ID">
							<input type="hidden" name="comment_parent" id="comment_parent" value="<?php echo esc_html((int)$parent_id);?>">
						</div>
					</div>
					<!-- #comment face system -->
				</div>
			</form>
		</div>
		<?php		
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	/**
	 * get the comment pagenavi
	 * 
	 * 
	 * @param string $class Class name
	 * @param bool $below The position where show.
	 * @return string
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 * 
	 */
	public static function get_comment_pagination( $args ) {
		$options = theme_options::get_options();
		/** Check the comment is open */
		$page_comments = get_option('page_comments');

		/** if comment is closed, return */
		if(!$page_comments) return;
		/** 
		 * defaults args
		 */
		$defaults = array(
			'classes'			=> 'comment-pagination',
			'cpaged'			=> max(1,get_query_var('cpage')),
			'cpp' 				=> get_option('comments_per_page'),
			'thread_comments'	=> get_option('thread_comments') ? true : false,
			// 'default_comments_page' => get_option('default_comments_page'),
			'default_comments_page' => 'oldest',
			'max_pages' 		=> get_comment_pages_count(null,get_option('comments_per_page'),get_option('thread_comments')),
			
		);
		$r = wp_parse_args($args,$defaults);
		extract($r,EXTR_SKIP);
		/** 
		 * if enable ajax
		 */
		if(isset($options['comment_ajax']) && $options['comment_ajax']['on'] == 1){
			$add_fragment = '&amp;pid=' . get_the_ID();
		}else{
			$add_fragment = false;
		}
		/** If has page to show me */
		if ( $max_pages > 1 ){
			$big = 999;
			$args = array(
				'base' 			=> str_replace($big,'%#%',get_comments_pagenum_link($big)), 
				'total'			=> $max_pages,
				'current'		=> $cpaged,
				'echo'			=> false, 
				'prev_text'		=> ___('&laquo;'),
				'next_text'   	=> ___('&raquo;'),
				'add_fragment'	=> $add_fragment,
			);
			$comments_page_links = paginate_links($args);
			$output = '<div class="'. $classes .'">'.$comments_page_links.'</div>';
			return $output;
		}
	}
	
	/** 
	 * the_post_0
	 */ 
	public static function the_post_0(){
		global $post;
		?>
		<div id="post-0"class="post no-results not-found mod">
			<?php echo status_tip('info','large',___( 'Sorry, I was not able to find what you need, what about look at other content :)')); ?>
		</div><!-- #post-0 -->

	<?php
	}
	/** 
	 * get_rank_data
	 */
	public static function get_rank_data($id = null){
		$content = array(
			'all' 			=> ___('All'),
			'daily' 		=> ___('Daily'),
			'weekly' 		=> ___('Weekly'),
			'monthly' 		=> ___('Monthly'),
		);
		if($id) return isset($content[$id]) ? $content[$id] : false;
		return $content;
	}
	/** 
	 * smart_page_pagination
	 */
	public static function smart_page_pagination($args = null){
		global $post,$page,$numpages;
		$output = null;
	
		$defaults = array(
			'add_fragment' => 'post-' . $post->ID
		);
		$r = wp_parse_args($args,$defaults);
		extract($r);
		$output['numpages'] = $numpages;
		$output['page'] = $page;
		/** 
		 * prev post
		 */
		$prev_post = get_previous_post(true);
		$prev_post = empty($prev_post) ? get_previous_post() : $prev_post;
		if(!empty($prev_post)){
			$output['prev_post'] = $prev_post;
		}
		/** 
		 * next post
		 */
		$next_post = get_next_post(true);
		$next_post = empty($next_post) ? get_next_post() : $next_post;
		// var_dump($next_post);
		if(!empty($next_post)){
			$output['next_post'] = $next_post;
		}		
		/** 
		 * exists multiple page
		 */
		if($numpages != 1){
			/** 
			 * if has prev page
			 */
			if($page > 1){
				$prev_page_number = $page - 1;
				$output['prev_page']['url'] = theme_features::get_link_page_url($prev_page_number,$add_fragment);
				$output['prev_page']['number'] = $prev_page_number;
			}
			/** 
			 * if has next page
			 */
			if($page < $numpages){
				$next_page_number = $page + 1;
				$output['next_page']['url'] = theme_features::get_link_page_url($next_page_number,$add_fragment);
				$output['next_page']['number'] = $next_page_number;
			}
		}
		// var_dump(array_filter($output));
		return array_filter($output);
	}

	public static function filter_prev_pagination_link($link,$page,$numpages){
		global $post;
		// var_dump($page);
		// var_dump($numpages);
		if($page > 1) return $link;
		$prev_post = get_previous_post(true);
		// var_dump($prev_post);
		$prev_post = empty($prev_post) ? get_previous_post() : $prev_post;
		if(empty($prev_post)) return $link;
		
		ob_start();
		?>
		<a href="<?php echo get_permalink($prev_post->ID);?>" class="nowrap page-numbers page-next btn btn-success grid-40 tablet-grid-40 mobile-grid-50 numbers-first" title="<?php echo esc_attr($prev_post->post_title);?>">
			<?php echo esc_html(___('&lsaquo; Previous'));?>
			-
			<?php echo esc_html($prev_post->post_title);?>
		</a>
		<?php
		$link = ob_get_contents();
		ob_end_clean();
		return $link;
	}
	public static function filter_next_pagination_link($link,$page,$numpages){
		global $post;
		// var_dump($page);
		// var_dump($numpages);
		if($page < $numpages) return $link;
		$next_post = get_next_post(true);
		// var_dump($prev_post);
		$next_post = empty($next_post) ? get_next_post() : $next_post;
		if(empty($next_post)) return $link;
		
		ob_start();
		?>
		<a href="<?php echo get_permalink($next_post->ID);?>" class="nowrap page-numbers page-next btn btn-success grid-40 tablet-grid-40 mobile-grid-50 numbers-first" title="<?php echo esc_attr($next_post->post_title);?>">
			<?php echo esc_html($next_post->post_title);?>
			-
			<?php echo esc_html(___('Next &rsaquo;'));?>
		</a>
		<?php
		$link = ob_get_contents();
		ob_end_clean();
		return $link;
	}
	public static function the_post_pagination(){
		global $post,$page,$numpages;
		?>
		<nav class="prev-next-pagination">
			<?php
			$prev_next_pagination = theme_smart_pagination::get_post_pagination();
			
			/** 
			 * exists prev page and next page, just show them
			 */
			if(isset($prev_next_pagination['prev_page']) && isset($prev_next_pagination['next_page'])){
				?>
				<a href="<?php echo esc_url($prev_next_pagination['prev_page']['url']);?>" class="prev-page nowrap btn btn-primary grid-parent grid-50 tablet-grid-50 mobile-grid-50"><?php echo esc_html(___('&larr; Preview page'));?></a>
				<a href="<?php echo esc_url($prev_next_pagination['next_page']['url']);?>" class="next-page nowrap btn btn-primary grid-parent grid-50 tablet-grid-50 mobile-grid-50"><?php echo esc_html(___('Next page &rarr;'));?></a>
				<?php
			/** 
			 * exists prev page, show prev page and next post
			 */
			}else if(isset($prev_next_pagination['prev_page'])){
				$grid_class = isset($prev_next_pagination['prev_post']) ? ' grid-50 tablet-grid-50 mobile-grid-50 ' : ' grid-100 tablet-grid-100 mobile-grid-100';
				?>
				<a href="<?php echo esc_url($prev_next_pagination['prev_page']['url']);?>" class="prev-page nowrap btn btn-primary grid-parent <?php echo $grid_class;?>"><?php echo esc_html(___('&larr; Preview page'));?></a>
				<?php
				if(isset($prev_next_pagination['prev_post'])){
					?>
					<a href="<?php echo get_permalink($prev_next_pagination['prev_post']->ID);?>" class="next-page nowrap btn btn-success grid-parent <?php echo $grid_class;?>"><span class="tx"><?php echo ___('Next post &rarr;');?></span><span class="next-post-tx hide"><?php echo esc_html(sprintf(___('%s &rarr;'),$prev_next_pagination['prev_post']->post_title));?></span></a>
					<?php
				}
			/** 
			 * exists next page, show next page and prev post
			 */
			}else if(isset($prev_next_pagination['next_page'])){
				$grid_class = isset($prev_next_pagination['prev_post']) ? ' grid-50 tablet-grid-50 mobile-grid-50 ' : ' grid-100 tablet-grid-100 mobile-grid-100';
				
				if(isset($prev_next_pagination['next_post'])){
					?>
					<a href="<?php echo get_permalink($prev_next_pagination['next_post']->ID);?>" class="prev-post nowrap btn btn-success grid-parent <?php echo $grid_class;?>"><span class="tx"><?php echo ___('&larr; Preview post');?></span><span class="prev-post-tx hide"><?php echo esc_html(sprintf(___('&larr; %s'),$prev_next_pagination['next_post']->post_title));?></span></a>
					<?php
				}
				?>
				<a href="<?php echo esc_url($prev_next_pagination['next_page']['url']);?>" class="next-page nowrap btn btn-primary grid-parent <?php echo $grid_class;?>"><?php echo esc_html(___('Next page &rarr;'));?></a>
				<?php
			/** 
			 * only exists next post and prev post, show them
			 */
			}else{

				$grid_class = isset($prev_next_pagination['prev_post']) && isset($prev_next_pagination['next_post']) ? ' grid-50 tablet-grid-50 mobile-grid-50 ' : ' grid-100 tablet-grid-100 mobile-grid-100';
				
				if(isset($prev_next_pagination['next_post'])){
					?>
					<a href="<?php echo get_permalink($prev_next_pagination['next_post']->ID);?>" class="prev-post nowrap btn btn-success grid-parent <?php echo $grid_class;?>"><span class="tx"><?php echo ___('&larr; Preview post');?></span><span class="prev-post-tx hide"><?php echo esc_html(sprintf(___('&larr; %s'),$prev_next_pagination['next_post']->post_title));?></span></a>
				<?php
				}
				if(isset($prev_next_pagination['prev_post'])){
					?>
					<a href="<?php echo get_permalink($prev_next_pagination['prev_post']->ID);?>" class="next-page nowrap btn btn-success grid-parent <?php echo $grid_class;?>"><span class="tx"><?php echo ___('Next post &rarr;');?></span><span class="next-post-tx hide"><?php echo esc_html(sprintf(___('%s &rarr;'),$prev_next_pagination['prev_post']->post_title));?></span></a>

				<?php
				}
			}
			?>
		</nav>
		<?php
	}
	/** 
	 * the_related_posts
	 */
	public static function the_related_posts($args_content = null,$args_query = null){
		global $post;
		
		$defaults_query = array(
			'posts_per_page' => 8
		);
		$args_query = wp_parse_args($args_query,$defaults_query);
		
		$defaults_content = array(
			'classes' => array('grid-25 tablet-grid-25 mobile-grid-50'),
		);
		$args_content = wp_parse_args($args_content,$defaults_content);
		
		$posts = theme_related_post::get_posts($args_query);
		if(!is_null_array($posts)){
			foreach($posts as $post){
				setup_postdata($post);
					echo self::archive_img_content($args_content);
			}
		}else{
			?>
			<div class="no-post page-tip"><?php echo status_tip('info',___('No data yet'));?></div>
			<?php
		}
		wp_reset_postdata();
	}



	/**
	 * get_page_pagenavi
	 * 
	 * 
	 * @return 
	 * @version 1.0.0
	 * @author KM@INN STUDIO
	 * 
	 */
	public static function get_page_pagenavi(){
		// var_dump( theme_features::get_pagination());
		global $page,$numpages;
		$output = null;
		if($numpages < 2) return;
		if($page < $numpages){
			$next_page = $page + 1;
			$output = '<a href="' . theme_features::get_link_page_url($next_page) . '" class="next_page">' . ___('Next page') . '</a>';
		}else{
			$prev_page = $page - 1;
			$output = '<a href="' . theme_features::get_link_page_url($prev_page) . '" class="prev_page">' . ___('Previous page') . '</a>';
		}
		$output = $output ? '<div class="singluar_page">' . $output . '</div>' : null;
		$args = array(
			'range' => 3
		);
		$output .= theme_features::get_pagination($args);
		return $output;
	}
}

?>
