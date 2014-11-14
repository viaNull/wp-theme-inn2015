<?php get_header();?>
<div class="container grid-container">
	<?php echo theme_functions::get_crumb();?>
	<div class="main grid-70 tablet-grid-70 mobile-grid-100">
		<?php theme_functions::page_content();?>
	</div>
	<?php get_sidebar();?>
</div>
<?php get_footer();?>