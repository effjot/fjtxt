<?php header("HTTP/1.1 404 Not Found"); ?>
<?php get_header() ?>

			<div class="hfeed">
				<div id="post-0" class="post hentry p1">
					<h3 class="entry-title"><?php _e('Nothing Found', 'blogtxt') ?></h3>
					<div class="entry-content">
						<p><?php _e('Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'blogtxt') ?></p>
					</div>
					<form id="searchform" method="get" action="<?php bloginfo('home') ?>">
						<div>
							<input id="s" name="s" type="text" value="<?php echo wp_specialchars(stripslashes($_GET['s']), true); ?>" tabindex="1" size="40" />
							<input id="searchsubmit" name="searchsubmit" type="submit" value="<?php _e('Search', 'blogtxt') ?>" tabindex="2" />
						</div>
					</form>
				</div><!-- #post-0 .post -->
			</div><!-- .hfeed -->
		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar() ?>
<?php get_footer() ?>