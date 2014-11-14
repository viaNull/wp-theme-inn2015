<?php get_header();?>
<div class="container grid-container">
	<div class="main grid-70 tablet-grid-70 mobile-grid-100">
		
		
		<dl class="mod mod-mixed">
			<dt class="mod-title"><?php echo esc_html(___('Latest posts'));?></dt>
			<?php if($paged > 1){ ?>
				<dd>
					<div class="area-pagination">
						<?php echo theme_functions::get_post_pagination('posts-pagination posts-pagination-top');?>
					</div>
				</dd>
			<?php } ?>
			<dd class="mod-body">
				<?php
				if(have_posts()){
					while(have_posts()){
						the_post();
						theme_functions::archive_content(array(
							'classes' => array('grid-parent grid-50 tablet-grid-50 mobile-grid-100')
						));
						?>
						
						<?php
					}/** end while */
	
				}else{/** end have_posts */
					echo status_tip('info',___('No data yet.'));
				}
				?>
				
			</dd>
		</dl>
		<div class="area-pagination">
			<?php echo theme_functions::get_post_pagination('posts-pagination posts-pagination-bottom');?>
		</div>
	</div>
	
	<?php get_sidebar();?>
</div>
<?php get_footer();?>