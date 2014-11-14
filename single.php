<?php get_header();?>
<div class="container grid-container">
	<?php echo theme_functions::get_crumb();?>
	<div class="main grid-70 tablet-grid-70 mobile-grid-100">
		<?php theme_functions::singular_content();?>
		<!-- related posts -->
		<dl class="related-posts mod">
			<dt class="mod-title tabtitle"><span class="icon-pie"></span><span class="after-icon"><?php echo esc_html(___('Maybe you would like them'));?></span></dt>
			<dd class="mod-body">
				<?php theme_functions::the_related_posts();?>
			</dd>
		</dl>
		<?php
		/** 
		 * theme_quick_comment
		 */
		theme_quick_comment::frontend_display();
		?>

	</div>
	<?php get_sidebar();?>
</div>
<?php get_footer();?>