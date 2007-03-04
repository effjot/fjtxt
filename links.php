<?php
/*
Template Name: Links Page
*/
?>
<?php get_header() ?>

			<div class="hfeed">

<?php the_post() ?>

				<div id="post-<?php the_ID(); ?>" class="<?php blogtxt_post_class() ?>">
					<h2 class="entry-title"><?php the_title() ?></h2>
					<?php if ( get_post_custom_values('authorlink') ) printf(__('<div class="archive-meta">By %1$s</div>', 'blogtxt'), blogtxt_author_link() ) // Add a key/value of "authorlink" to show an author byline on a page ?>
					<div class="entry-content">
<?php the_content() ?>

						<ul id="linkcats" class="content-column">
<?php if ( function_exists('wp_list_bookmarks') ) : wp_list_bookmarks('categorize=true&title_before=<h3>&title_after=</h3>'); else : // Shows WordPress 2.1.x links, or . . . ?>
<?php // Shows WordPress 2.0.x links
$link_cats = $wpdb->get_results("SELECT cat_id, cat_name FROM $wpdb->linkcategories");
foreach ($link_cats as $link_cat) :
?>
							<li id="linkcat-<?php echo $link_cat->cat_id; ?>">
								<h3><?php echo $link_cat->cat_name; ?></h3>
								<ul>
									<?php wp_get_links($link_cat->cat_id); ?>
								</ul>
							</li>
<?php endforeach ?>
<?php endif ?>
						</ul>
<?php edit_post_link(__('Edit this entry.', 'blogtxt'),'<p class="entry-edit">','</p>') ?>

					</div>
				</div><!-- .post -->

<?php if ( get_post_custom_values('comments') ) comments_template() // Add a key/value of "comments" to load comments on a page ?>

			</div><!-- .hfeed -->
		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar() ?>
<?php get_footer() ?>