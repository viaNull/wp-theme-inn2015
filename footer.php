	<footer class="footer">
		<div class="grid-container">
			<div class="widget-area">
				<?php
				if(is_active_sidebar('widget-area-footer')){
					echo theme_cache::get_widget_sidebars('widget-area-footer');
				}
				?>
			</div>

			<p class="footer-meta copyright">
				<?php echo sprintf(___('Copyright &copy; %s %s.'),'<a href="' . esc_url(home_url()) . '">' .esc_html(get_bloginfo('name')) . '</a>',esc_html(current_time('Y')));?>
				<?php echo sprintf(___('Theme %s by %s.'),'<a href="' . esc_url(theme_features::get_theme_info('ThemeURI')) . '" target="_blank" rel="nofollow">' . theme_features::get_theme_info('name') . '</a>','<a href="http://inn-studio.com" target="_blank" rel="nofollow">' . esc_html(___('INN STUDIO')) . '</a>');?>
				<?php echo sprintf(___('Powered by %s.'),'<a href="http://www.wordpress.org" target="_blank" rel="nofollow">WordPress</a>');?>
			</p>
		</div>
	</footer>
	
	<div id="qrcode" class="hide-no-js hide-on-tablet hide-on-mobile" title="<?php echo ___('Click to show QR code for this page');?>">
		<div id="qrcode-box"></div>
		<div id="qrcode-zoom" class="hide">
			<div id="qrcode-zoom-code"></div>
			<h3><?php echo ___('Scan the QR code to visit this page');?></h3>
		</div>
	</div>

	<?php wp_footer();?>
</body></html>