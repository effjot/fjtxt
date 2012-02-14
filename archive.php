<?php get_header() ?>

<div class="hfeed">

  <?php the_post() ?>

  <?php if (is_day()) : ?>
    <h2 class="page-title">
      <?php _e('Daily Archives', 'blogtxt') ?>
      <span class="page-subtitle">
        <?php the_time(__('l, F Y', 'blogtxt')) ?>
      </span>
    </h2>

  <?php elseif (is_month()) : ?>
    <h2 class="page-title">
      <?php _e('Monthly Archives', 'blogtxt') ?>
      <span class="page-subtitle">
        <?php the_time(__('F Y', 'blogtxt')) ?>
      </span>
    </h2>

  <?php elseif (is_year()) : ?>
    <h2 class="page-title">
      <?php _e('Yearly Archives', 'blogtxt') ?>
      <span class="page-subtitle">
        <?php the_time(__('Y', 'blogtxt')) ?>
      </span>
    </h2>

  <?php elseif (is_author()) : ?>
    <h2 class="page-title">
      <?php _e('Author Archives', 'blogtxt') ?>
      <span class="page-subtitle">
        <?php blogtxt_author_hCard() ?>
      </span>
    </h2>

    <div class="archive-meta">
      <?php
        if (!(''== $authordata->user_description)) {
          echo apply_filters('archive_meta', $authordata->user_description);
        } ?>
    </div>

  <?php elseif (is_category()) : ?>
    <h2 class="page-title">
      <?php _e('Category Archives:', 'blogtxt') ?>
      <span class="page-subtitle">
        <?php echo single_cat_title(); ?>
      </span>
    </h2>

  <?php elseif (is_tag()) : ?>
    <h2 class="page-title">
      <span class="archive-meta">
        <?php _e('Tag Archives:', 'blogtxt') ?>
      </span>
      <span class="page-subtitle">
        <?php single_tag_title(); ?>
      </span>
    </h2>

  <?php elseif (isset($_GET['paged']) && !empty($_GET['paged'])) : ?>
    <h2 class="page-title">
      <?php _e('Archives', 'blogtxt') ?>
      <?php printf(__('%1$s Archives', 'blogtxt'),
                   wp_specialchars(get_the_title(), 'double')) ?>
    </h2>

  <?php endif; ?>

  <?php rewind_posts() ?>

  <?php while (have_posts()) : the_post(); ?>

    <div id="post-<?php the_ID() ?>" class="<?php blogtxt_post_class() ?>">
      <h3 class="entry-title">
        <a href="<?php the_permalink() ?>"
           title="<?php printf(__('Permalink to %s', 'blogtxt'),
                               wp_specialchars(get_the_title(), 1)) ?>"
           rel="bookmark">
          <?php the_title() ?>
        </a>
      </h3>

      <div class="entry-content">
        <?php
          if ((function_exists('has_post_thumbnail')) &&
              (has_post_thumbnail())) {
            the_post_thumbnail('thumbnail');
          }

          the_excerpt();
         /* Argumente gibt's wohl nicht (mehr)
            the_excerpt('<span class="more-link">'.
            __('Continue Reading', 'blogtxt').'</span>');
            Ist jetzt in functions.php */
        ?>
      </div>

      <div class="entry-meta">
        <span class="entry-date">
          <?php _e('Posted', 'blogtxt') ?>
          <abbr class="published"
                title="<?php the_time('Y-m-d\TH:i:sO'); ?>">
            <?php unset($previousday);
                  printf(__('%1$s', 'blogtxt'), the_date('d.m.y', false)); ?>
          </abbr>
        </span>

        <span class="meta-sep">&lang;</span>

        <?php if (!is_author()) :
          blogtxt_author_link(); // Displays if NOT author archive page
        ?>
          <span class="meta-sep">|</span>
        <?php endif ?>

        <span class="entry-category">
        <?php
          if (!is_category()) :
            echo the_category(', ');
          else :
            $other_cats = blogtxt_other_cats(', ');
            if (!empty($other_cats)) : // Hides the current category if
                                       // category archive
              echo $other_cats;
        ?>
              </span>
              <span class="meta-sep">|</span>
        <?php
            else :
              echo '</span>';
            endif;
         endif;
        ?>

        <span class="entry-comments">
          <?php
            comments_popup_link(__('Comments (0)', 'blogtxt'),
                                __('Comments (1)', 'blogtxt'),
                                __('Comments (%)', 'blogtxt'))
          ?>
        </span>

        <?php if (get_the_tags()) : ?>
          <span class="meta-sep">|</span>
          <span class="tag-links">
            <?php
            if (!is_tag()) {
              echo the_tags(__('Tagged: ', 'blogtxt'), ", ", "");
            } else {
              $other_tags = blogtxt_other_tags(', ');
              printf(__('Also tagged: %s', 'blogtxt'), $other_tags);
            }
            ?>
          </span>
        <?php endif ?>

        <?php
          edit_post_link(__('Edit', 'blogtxt'),
                         "<span class='meta-sep'>|</span>" .
                           "<span class='entry-edit'>",
                         "</span>");
        ?>

        &rang;

      </div>

    </div><!-- .post -->

  <?php endwhile ?>


  <div id="nav-below" class="navigation">
    <div class="nav-previous">
    <?php next_posts_link(__('&laquo; Earlier posts', 'blogtxt')) ?>
    </div>
    <div class="nav-next">
      <?php previous_posts_link(__('Later posts &raquo;', 'blogtxt')) ?>
    </div>
  </div>

</div><!-- .hfeed -->
</div><!-- #content -->
</div><!-- #container -->

<?php get_sidebar() ?>
<?php get_footer() ?>

