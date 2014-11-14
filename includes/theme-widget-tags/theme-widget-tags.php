<?php
/**
 * @version 1.0.1
 */
add_action('widgets_init','widget_hot_tags::register_widget' );
class widget_hot_tags extends WP_Widget{
	public static $iden = 'widget_hot_tags';
	function __construct(){
		$this->alt_option_name = self::$iden;
		parent::__construct(
			self::$iden,
			___('Popular tags <small>(Custom)</small>'),
			array(
				'classname' => self::$iden,
				'description'=> ___('Show the most popular tags.'),
			)
		);
	}
	public static function frontend_display($args = null,$instance = null){
		$cache_id = md5(serialize($instance));
		
		$cache = theme_cache::get($cache_id);

		if($cache){
			echo $cache;
			return;
		}
		$smallest = 11;
		$largest = 20;
		$unit = 'pt';
		$number = $instance['number'];
		$exclude_ids = isset($instance['ids']) ? $instance['ids'] : null;
		$tag_links = array();
		$sticky_links = array();
		if(!empty($exclude_ids)){
			foreach($exclude_ids as $k => $v){
				$sticky_name = isset($sticky_names[$k]) ? $sticky_names[$k] : null;
				$sticky_links[] = '<a href="' . get_tag_link($v) . '" class="sticky-tag">' . esc_html($sticky_name) . '</a>';
			}
		}
		$tags = get_terms('post_tag',array(
			'orderby' => 'count',
			'number'    => $number,
			'order' => 'desc',
			'pad_counts' => true,
			'exclude' => $exclude_ids
		));
		if(!empty($tags)){
			$counts = array();
			$real_counts = array(); // For the alt tag
			foreach ( (array) $tags as $key => $tag ) {
				$real_counts[ $key ] = $tag->count;
				$counts[ $key ] = $tag->count;
			}

			$min_count = min( $counts );
			$spread = max( $counts ) - $min_count;
			if ( $spread <= 0 )
				$spread = 1;
			$font_spread = $largest - $smallest;
			if ( $font_spread < 0 )
				$font_spread = 1;
			$font_step = $font_spread / $spread;
			
			foreach ( $tags as $key => $tag ) {
				$count = $counts[ $key ];
				ob_start();
				
				?>
				<a 
					class="hot-tag" 
					href="<?php echo get_tag_link($tag->term_id);?>"
					style="
						font-size:<?php echo str_replace( ',', '.', ( $smallest + ( ( $count - $min_count ) * $font_step ) ) ),$unit;?>;
						color:rgb(<?php echo mt_rand(50,200);?>,<?php echo mt_rand(50,200);?>,<?php echo mt_rand(50,200);?>);"
				><?php echo esc_html($tag->name);?></a>
				<?php
				$tag_links[] = html_compress(ob_get_contents());
				ob_end_clean();
			}
		}
		$tags =  array_merge($tag_links,$sticky_links);
		if(!empty($tags)){
			shuffle($tags);
			$cache = implode('',$tags);
		}else{
			$cache = status_tip('info',___('No data yet.'));
		}
		theme_cache::set($cache_id,$cache);
		echo $cache;
	}
	function widget($args,$instance){
		extract($args);
		$instance = wp_parse_args(
			(array)$instance,
			array(
				'title' => '',
				'number' => 20,
				'sticky' => array(),
			)
		);
		echo $before_widget;
		if(!empty($instance['title'])){
			echo $before_title;
			?>
			<span class="icon-tags"></span><span class="after-icon"><?php echo esc_html($instance['title']);?></span>
			<?php
			echo $after_title;
		}
		?>
		<div class="widget-content">
		<?php self::frontend_display($args,$instance); ?>
		</div>
		<?php
		echo $after_widget;
	}
	function form($instance){
		$instance = wp_parse_args(
			(array)$instance,
			array(
				'title'=>___('Hot tags'),
				'number' => 20,
				'sticky' => array(),
			)
		);
		$sticky_tx = implode(PHP_EOL,(array)$instance['sticky']);
		?>
		<p>
			<label for="<?php echo esc_attr(self::get_field_id('title'));?>"><?php echo esc_html(___('Title (optional)'));?></label>
			<input 
				id="<?php echo esc_attr(self::get_field_id('title'));?>"
				class="widefat"
				name="<?php echo esc_attr(self::get_field_name('title'));?>" 
				type="text" 
				value="<?php echo esc_attr($instance['title']);?>" 
				placeholder="<?php echo esc_attr(___('Title (optional)'));?>"
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr(self::get_field_id('number'));?>"><?php echo esc_html(___('Tags number (required)'));?></label>
			<input 
				id="<?php echo esc_attr(self::get_field_id('number'));?>" 
				class="widefat"
				name="<?php echo esc_attr(self::get_field_name('number'));?>" 
				type="number" 
				value="<?php echo esc_attr($instance['number']);?>" 
				placeholder="<?php echo esc_attr(___('Tags number (required)'));?>"
				required
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr(self::get_field_id('sticky'));?>"><?php echo esc_html(___('Sticky tags (optional, one tag per line)'));?></label>
			<textarea 
				id="<?php echo esc_attr(self::get_field_id('sticky'));?>" 
				class="widefat"
				name="<?php echo esc_attr(self::get_field_name('sticky'));?>" 
				placeholder="<?php echo esc_attr(___('Sticky tags (optional, one tag per line)'));?>"
			><?php echo esc_attr($sticky_tx);?></textarea>
		</p>
		<?php
	}
	function update($new_instance,$old_instance){
		$instance = wp_parse_args($new_instance,$old_instance);
		/** 
		 * text tags to array tags
		 */
		$sticky = self::textarea2array($old_instance['sticky']);
		if(!empty($sticky)){
			$instance['ids'] = $sticky;
		}
		
		return $instance;
	}
	public static function textarea2array($text){
		if(empty($text)) return null;
		$tag_names = explode(PHP_EOL,$text);
		sort($tag_names);
		$holder = array();
		foreach($tag_names as $tag_name){
			$holder[] = '%s';
		}
		$holder = implode(',',$holder);
		global $wpdb;
		$sql = $wpdb->prepare(
			"
			SELECT `term_id` FROM $wpdb->terms
			WHERE `name` IN ($holder)
			ORDER BY `name` ASC
			",
			$tag_names
		);
		return $wpdb->get_col($sql);
	}
	public static function register_widget(){
		register_widget(self::$iden);
	}

}