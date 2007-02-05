<?php get_header() ?>

			<div class="hfeed">

<?php the_post() ?>

				<div id="post-<?php the_ID(); ?>" class="<?php blogtxt_post_class() ?>">
					<h2 class="entry-title"><?php the_title(); ?></h2>
					<?php if ( get_post_custom_values('authorlink') ) printf(__('<div class="archive-meta">By %1$s</div>', 'blogtxt'), blogtxt_author_link() ) ?>
					<div class="entry-content">
<?php the_content() ?>

<?php link_pages('<div class="page-link">'.__('Pages: ', 'blogtxt'), '</div>', 'number'); ?>

<?php edit_post_link(__('Edit this entry.', 'blogtxt'),'<p class="entry-edit">','</p>') ?>

					</div>
				</div>

<?php if ( get_post_custom_values('comments') ) comments_template() ?>

			</div>
		</div>
	</div>

<?php get_sidebar() ?>
<?php get_footer() ?>