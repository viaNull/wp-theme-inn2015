<div class="sidebar widget-area grid-30 tablet-grid-30 mobile-grid-100" role="complementary">
	<?php
	/** 
	 * home widget
	 */
	if(is_home() && is_active_sidebar('widget-area-home')){
		dynamic_sidebar('widget-area-home');
	/** 
	 * archive widget
	 */
	}else if((is_category() || is_archive() || is_search()) && is_active_sidebar('widget-area-archive')){
		dynamic_sidebar('widget-area-archive');
	/** 
	 * post widget
	 */
	}else if( is_singular('post') && is_active_sidebar('widget-area-post')){
		dynamic_sidebar('widget-area-post');
	/** 
	 * page widget
	 */
	}else if( is_page() && is_active_sidebar('widget-area-page')){
		dynamic_sidebar('widget-area-page');
	/** 
	 * 404 widget
	 */
	}else if( is_404() && is_active_sidebar('widget-area-404')){
		dynamic_sidebar('widget-area-404');
	}
	?>
</div><!-- /.widget-area -->
