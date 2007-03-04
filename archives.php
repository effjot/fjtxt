<?php
/*
Template Name: Archives Page
*/
?>
<?php get_header() ?>

			<div class="hfeed">

<?php the_post() ?>

				<div id="post-<?php the_ID() ?>" class="<?php blogtxt_post_class() ?>">
					<h2 class="entry-title"><?php the_title() ?></h2>
					<?php if ( get_post_custom_values('authorlink') ) printf(__('<div class="archive-meta">By %1$s</div>', 'blogtxt'), blogtxt_author_link() ) // Add a key/value of "authorlink" to show an author byline on a page ?>
					<div class="entry-content">
<?php the_content(); ?>

						<ul class="alignleft content-column">
							<li>
								<h3><?php _e('Category Archives', 'blogtxt') ?></h3>
								<ul>
								<?php if ( function_exists('wp_list_categories') ) : 
									wp_list_categories('title_li=&orderby=name&show_count=1&use_desc_for_title=1&feed_image='.get_bloginfo('template_url').'/images/feed.png'); 	else :
									wp_list_cats('sort_column=name&optioncount=1&feed=(RSS)&feed_image='.get_bloginfo('template_url').'/images/feed.png&hierarchical=1'); endif; ?>
								</ul>
							</li>
						</ul>

						<ul class="alignleft content-column">
							<li>
								<h3><?php _e('Monthly Archives', 'blogtxt') ?></h3>
								<ul>
									<?php wp_get_archives('type=monthly&show_post_count=1') ?>
								</ul>
							</li>
						</ul>
<?php edit_post_link(__('Edit this entry.', 'blogtxt'),'<p class="entry-edit">','</p>') ?>

					</div>
				</div><!-- .post -->

<?php if ( get_post_custom_values('comments') ) comments_template() // Add a key/value of "comments" to load comments on a page ?>

			</div><!-- .hfeed -->
		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>