<!DOCTYPE html><html <?php language_attributes(); ?>><head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php echo theme_features::get_wp_title();?></title>
	<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /><![endif]-->
	<meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width" />
	<meta name="author" content="INN STUDIO" />
	<meta http-equiv="Cache-Control" content="no-transform" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php echo theme_features::get_theme_css('modules/unsemantic-grid-responsive-tablet-no-ie7','normal');?>
	<?php echo theme_features::get_theme_css('frontend/fonts','normal');?>
	<?php echo theme_features::get_theme_css('frontend/style','normal',true);?>
	<link rel="shortcut icon" href="<?php echo theme_features::get_theme_images_url('frontend/favicon.ico',true);?>" type="image/x-icon" />
	<?php wp_head();?>
</head>
<body <?php body_class(); ?>>
<!--[if lte IE 8]>
<style>
#ie-notice{
	clear: both; 
	color: #555;
	font-size: 15px; 
	padding: 5px;
	text-align: center;
}
</style>
<div id="ie-notice">
	<?php echo status_tip('ban','middle',sprintf(___('We recommend that you upgrade your web browser so that you can get the most of the %s website. Upgrading your browser should only take a few minutes. Please download the latest version of one of the following free browsers:'),get_bloginfo('name')) . '
	<p class="ie-notice-browsers">
		<a href="https://chrome.google.com" target="_blank" title="' . ___('Chrome') . '"><img src="https://cdn1.iconfinder.com/data/icons/all_google_icons_symbols_by_carlosjj-du/64/chrome.png" alt="' . ___('Chrome') . '"/></a>
		<a href="https://firefox.com" target="_blank" title="' . ___('Firefox') . '"><img src="https://aful.org/media/image/interop/mozilla-firefox_logo.png" alt="' . ___('Firefox') . '"/></a>
		<a href="http://opera.com" target="_blank" title="' . ___('Opera') . '"><img src="https://cdn1.iconfinder.com/data/icons/android-png/64/Android-Opera-Mini.png" alt="' . ___('Opera') . '"/></a>
		<a href="http://www.maxthon.com" target="_blank" title="' . ___('Maxthon') . '"><img src="http://upload.wikimedia.org/wikipedia/zh/thumb/0/02/Maxthonlogo.png/64px-Maxthonlogo.png" alt="' . ___('Maxthon') . '"/></a>
		<a href="http://windows.microsoft.com/' . strtolower(get_bloginfo('language')) . '/internet-explorer/download-ie" target="_blank" title="' . ___('Internet Explorer') . '"><img src="http://upload.wikimedia.org/wikipedia/en/thumb/1/10/Internet_Explorer_7_Logo.png/64px-Internet_Explorer_7_Logo.png" alt="' . ___('Internet Explorer') . '"/></a>
	</p>
	'
	);?>
</div>  
<![endif]-->
<header class="grid-container">

</header>

<div class="header-bar-container">
	<div class="header-bar-bg">
	<div class="header-bar grid-container">
		<!-- .left -->
		<div class="left">
			<h1 class="blog-name hide-on-mobile" title="<?php echo esc_attr(get_bloginfo('description'));?>"><a href="<?php echo esc_url(home_url());?>"><span class="icon-home"></span><span class="after-icon"><?php echo esc_html(get_bloginfo('name'));?></span></a></h1>
			<h2 class="blog-description hide"><?php echo esc_attr(get_bloginfo('description'));?></h2>
			<div class="menu-desktop hide-on-mobile">
				<?php
				/** 
				 * menu tools
				 */
				echo theme_cache::get_nav_menu(array(
					'theme_location' => 'menu-header',
					'menu_class' => 'menu',
					'container' => 'nav',
					'menu_id' => 'menu-header',
				));
				?>
			</div>
			
			<div class="menu-mobile-toggle hide-on-desktop hide-on-tablet">
				<a href="javascript:void(0);" class="toggle" data-target="#menu-mobile-header">
					<span class="icon-list"></span><span class="after-icon"><?php echo esc_html(___('Navigation'));?></span>
				</a>
			</div>
		</div><!-- .left -->
		<!-- .right -->
		<div class="right">
			<div class="left">
				<?php
				/** 
				 * menu tools
				 */
				echo theme_cache::get_nav_menu(array(
					'theme_location' => 'menu-tools',
					'menu_class' => 'menu',
					'container' => 'nav',
					'menu_id' => 'menu-tools',
				));
				?>
			</div>
			<!-- .search -->
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="header-bar-meta meta-extra fm-search">
				<div class="box">
					<input id="header-bar-search" type="search" class="form-control" title="<?php echo esc_attr(___('Search'));?>" name="s" placeholder="<?php echo esc_attr(___('Search keyword'));?>" value="<?php echo esc_attr(get_search_query())?>" required />
					<button type="submit" class="submit" title="<?php echo esc_attr(___('Submit'));?>"><span class="icon-search"></span><span class="after-icon hide"><?php echo esc_html(___('Search'));?></span></button>
				</div>
				<label for="header-bar-search">
					<span class="icon-search"></span><span class="after-icon hide"><?php echo esc_html(___('Search'));?></span>
				</label>
			</form>
		</div><!-- .right -->
		<div class="clr"></div>
		<?php
		echo theme_cache::get_nav_menu(
			array(
				'theme_location' => 'menu-header-mobile',
				'menu_class' => 'menu menu-mobile hide clr',
				'container' => 'nav',
				'menu_id' => 'menu-mobile-header',
			)
		);
		?>
	</div><!-- .grid-container -->
</div><!-- .header-bar-bg -->
</div><!-- .header-bar-container -->
