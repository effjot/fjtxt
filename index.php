<?php get_header() ?>

<div class="hfeed">

  <?php
  while (have_posts()) :

    the_post()
  ?>

  <div id="post-<?php the_ID() ?>" class="<?php blogtxt_post_class() ?>">

    <h2 class="entry-title">
      <a href="<?php the_permalink() ?>"
         title="<?php printf(__('Permalink to %s', 'blogtxt'), esc_attr(get_the_title())) ?>"
         rel="bookmark">
        <?php the_title() ?>
      </a>
    </h2>

    <div class="entry-content">
      <?php
        the_content('<span class="more-link">' .
                    __('Continue Reading', 'blogtxt').' &rang;</span>');

        wp_link_pages('before=<div class="page-link">' . __('Pages: ', 'blogtxt') .
                      '&after=</div>&next_or_number=number');
      ?>
    </div>

    <div class="entry-meta">

      <span class="entry-date">
        <?php _e('Posted', 'blogtxt') ?>
        <abbr class="published" title="<?php the_time('Y-m-d\TH:i:sO'); ?>">
          <?php unset($previousday); printf(__('%1$s', 'blogtxt'), the_date('', false)) ?>
        </abbr>
      </span>

      <span class="meta-sep">&lang;</span>

      <?php blogtxt_author_link(); // Function for author link option ?>

      <span class="meta-sep">|</span>

      <span class="entry-category"><?php the_category(', ') ?></span>

      <span class="meta-sep">|</span>

      <span class="entry-comments">
        <?php comments_popup_link(__('Comments (0)', 'blogtxt'), __('Comments (1)', 'blogtxt'), __('Comments (%)', 'blogtxt')) ?>
      </span>

      <?php if (get_the_tags()) : ?>
      <span class="meta-sep">|</span>
      <span class="entry-tags"><?php the_tags(__('Tagged: ', 'blogtxt'), ", ", "") ?></span>
      <?php endif ?>

      <?php edit_post_link(__('Edit', 'blogtxt'), "\t\t\t\t\t<span class=\"meta-sep\">|</span>\n\t\t\t\t\t<span class='entry-edit'>", "</span>"); ?>

      <span class="meta-sep">&rang;</span>

    </div>

  </div>

  <?php
  endwhile
  ?>

  <div id="nav-below" class="navigation">
    <div class="nav-previous"><?php next_posts_link(__('&laquo; Earlier posts', 'blogtxt')) ?></div>
    <div class="nav-next"><?php previous_posts_link(__('Later posts &raquo;', 'blogtxt')) ?></div>
  </div>

</div><!-- .hfeed -->
</div><!-- #content -->
</div><!-- #container -->

<?php get_sidebar() ?>
<?php get_footer() ?>
