<?php
function blogtxt_globalnav() {
	echo "<div id=\"globalnav\"><ul id=\"menu\">";
	if ( !is_home() || is_paged() ) { ?><li class="page_item home_page_item"><a href="<?php bloginfo('home') ?>" title="<?php echo wp_specialchars(get_bloginfo('name'), 1) ?>"><?php _e('Home', 'blogtxt') ?></a></li><?php }
	$menu = wp_list_pages('title_li=&sort_column=post_title&echo=0');
	echo str_replace(array("\r", "\n", "\t"), '', $menu);
	echo "</ul></div>\n";
}

function blogtxt_admin_hCard() {
	global $wpdb, $admin_info;
	$admin_info = get_userdata(1);
	echo '<span class="vcard"><a class="url fn n" href="' . $admin_info->user_url . '"><span class="given-name">' . $admin_info->first_name . '</span> <span class="family-name">' . $admin_info->last_name . '</span></a></span>';
}

function blogtxt_author_hCard() {
	global $wpdb, $authordata;
	echo '<span class="entry-author author vcard"><a class="url fn" href="' . get_author_link(false, $authordata->ID, $authordata->user_nicename) . '" title="View all posts by ' . $authordata->display_name . '">' . get_the_author() . '</a></span>';
}

function blogtxt_body_class( $print = true ) {
	global $wp_query, $current_user;

	$c = array('wordpress');

	blogtxt_date_classes(time(), $c);

	is_home()       ? $c[] = 'home'       : null;
	is_archive()    ? $c[] = 'archive'    : null;
	is_date()       ? $c[] = 'date'       : null;
	is_search()     ? $c[] = 'search'     : null;
	is_paged()      ? $c[] = 'paged'      : null;
	is_attachment() ? $c[] = 'attachment' : null;
	is_404()        ? $c[] = 'four04'     : null;

	if ( is_single() ) {
		the_post();
		$c[] = 'single';
		if ( isset($wp_query->post->post_date) )
			blogtxt_date_classes(mysql2date('U', $wp_query->post->post_date), $c, 's-');
		foreach ( (array) get_the_category() as $cat )
			$c[] = 's-category-' . $cat->category_nicename;
			$c[] = 's-author-' . get_the_author_login();
		rewind_posts();
	}

	else if ( is_author() ) {
		$author = $wp_query->get_queried_object();
		$c[] = 'author';
		$c[] = 'author-' . $author->user_nicename;
	}
	
	else if ( is_category() ) {
		$cat = $wp_query->get_queried_object();
		$c[] = 'category';
		$c[] = 'category-' . $cat->category_nicename;
	}

	else if ( is_page() ) {
		the_post();
		$c[] = 'page';
		$c[] = 'page-author-' . get_the_author_login();
		rewind_posts();
	}

	if ( $current_user->ID )
		$c[] = 'loggedin';
		
	$c = join(' ', apply_filters('body_class',  $c));

	return $print ? print($c) : $c;
}

function blogtxt_post_class( $print = true ) {
	global $post, $blogtxt_post_alt;

	$c = array('hentry', "p$blogtxt_post_alt", $post->post_type, $post->post_status);

	$c[] = 'author-' . get_the_author_login();
	
	foreach ( (array) get_the_category() as $cat )
		$c[] = 'category-' . $cat->category_nicename;

	blogtxt_date_classes(mysql2date('U', $post->post_date), $c);

	if ( ++$blogtxt_post_alt % 2 )
		$c[] = 'alt';
		
	$c = join(' ', apply_filters('post_class', $c));

	return $print ? print($c) : $c;
}
$blogtxt_post_alt = 1;

function blogtxt_comment_class( $print = true ) {
	global $comment, $post, $blogtxt_comment_alt;

	$c = array($comment->comment_type);

	if ( $comment->user_id > 0 ) {
		$user = get_userdata($comment->user_id);

		$c[] = "byuser commentauthor-$user->user_login";

		if ( $comment->user_id === $post->post_author )
			$c[] = 'bypostauthor';
	}

	blogtxt_date_classes(mysql2date('U', $comment->comment_date), $c, 'c-');
	if ( ++$blogtxt_comment_alt % 2 )
		$c[] = 'alt';

	$c[] = "c$blogtxt_comment_alt";

	if ( is_trackback() ) {
		$c[] = 'trackback';
	}

	$c = join(' ', apply_filters('comment_class', $c));

	return $print ? print($c) : $c;
}

function blogtxt_date_classes($t, &$c, $p = '') {
	$t = $t + (get_settings('gmt_offset') * 3600);
	$c[] = $p . 'y' . gmdate('Y', $t);
	$c[] = $p . 'm' . gmdate('m', $t);
	$c[] = $p . 'd' . gmdate('d', $t);
	$c[] = $p . 'h' . gmdate('h', $t);
}

function blogtxt_other_cats($glue) {
	$current_cat = single_cat_title('', false);
	$separator = "\n";
	$cats = explode($separator, get_the_category_list($separator));

	foreach ( $cats as $i => $str ) {
		if ( strstr($str, ">$current_cat<") ) {
			unset($cats[$i]);
			break;
		}
	}

	if ( empty($cats) )
		return false;

	return trim(join($glue, $cats));
}

function widget_blogtxt_search($args) {
	extract($args);
?>
		<?php echo $before_widget ?>
			<?php echo $before_title ?><label for="s"><?php _e('Blog Search', 'blogtxt') ?></label><?php echo $after_title ?>
			<form id="searchform" method="get" action="<?php bloginfo('home') ?>">
				<div>
					<input id="s" name="s" type="text" value="<?php echo wp_specialchars(stripslashes($_GET['s']), true) ?>" size="10" />
					<input id="searchsubmit" name="searchsubmit" type="submit" value="<?php _e('Find', 'blogtxt') ?>" />
				</div>
			</form>
		<?php echo $after_widget ?>
<?php
}

function widget_blogtxt_meta($args) {
	extract($args);
	$options = get_option('widget_meta');
	$title = empty($options['title']) ? __('Meta', 'blogtxt') : $options['title'];
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
				<?php wp_register() ?>
				<li><?php wp_loginout() ?></li>
				<?php wp_meta() ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
}

function widget_blogtxt_homelink($args) {
	extract($args);
	$options = get_option('widget_blogtxt_homelink');
	$title = empty($options['title']) ? __('&laquo; Home', 'blogtxt') : $options['title'];
?>
<?php if ( !is_home() || is_paged() ) { ?>
		<?php echo $before_widget; ?>
			<?php echo $before_title ?><a href="<?php bloginfo('home') ?>" title="<?php echo wp_specialchars(get_bloginfo('name'), 1) ?>"><?php echo $title ?></a><?php echo $after_title ?>
		<?php echo $after_widget; ?>
<?php } ?>
<?php
}

function widget_blogtxt_homelink_control() {
	$options = $newoptions = get_option('widget_blogtxt_homelink');
	if ( $_POST["homelink-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["homelink-title"]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_blogtxt_homelink', $options);
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
?>
		<p style="text-align:left;"><?php _e('Adds a link to the home page on every page <em>except</em> the home.', 'blogtxt'); ?></p>
		<p><label for="homelink-title"><?php _e('Link Text:'); ?> <input style="width: 175px;" id="homelink-title" name="homelink-title" type="text" value="<?php echo $title; ?>" /></label></p>
		<input type="hidden" id="homelink-submit" name="homelink-submit" value="1" />
<?php
}

function widget_blogtxt_rsslinks($args) {
	extract($args);
	$options = get_option('widget_blogtxt_rsslinks');
	$title = empty($options['title']) ? __('RSS Links', 'blogtxt') : $options['title'];
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
				<li><a href="<?php bloginfo('rss2_url') ?>" title="<?php echo wp_specialchars(get_bloginfo('name'), 1) ?> RSS 2.0 Feed" rel="alternate" type="application/rss+xml"><?php _e('All posts', 'blogtxt') ?></a></li>
				<li><a href="<?php bloginfo('comments_rss2_url') ?>" title="<?php echo wp_specialchars(bloginfo('name'), 1) ?> Comments RSS 2.0 Feed" rel="alternate" type="application/rss+xml"><?php _e('All comments', 'blogtxt') ?></a></li>
			</ul>
		<?php echo $after_widget; ?>
<?php
}

function widget_blogtxt_rsslinks_control() {
	$options = $newoptions = get_option('widget_blogtxt_rsslinks');
	if ( $_POST["rsslinks-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["rsslinks-title"]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_blogtxt_rsslinks', $options);
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
?>
			<p><label for="rsslinks-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="rsslinks-title" name="rsslinks-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="rsslinks-submit" name="rsslinks-submit" value="1" />
<?php
}

function widget_blogtxt_links() {
	if ( function_exists('wp_list_bookmarks') ) {
		wp_list_bookmarks(array('title_before'=>'<h3>', 'title_after'=>'</h3>', 'show_images'=>true));
	} else {
		global $wpdb;

		$cats = $wpdb->get_results("
			SELECT DISTINCT link_category, cat_name, show_images, 
				show_description, show_rating, show_updated, sort_order, 
				sort_desc, list_limit
			FROM `$wpdb->links` 
			LEFT JOIN `$wpdb->linkcategories` ON (link_category = cat_id)
			WHERE link_visible =  'Y'
				AND list_limit <> 0
			ORDER BY cat_name ASC", ARRAY_A);
	
		if ($cats) {
			foreach ($cats as $cat) {
				$orderby = $cat['sort_order'];
				$orderby = (bool_from_yn($cat['sort_desc'])?'_':'') . $orderby;

				echo '	<li id="linkcat-' . $cat['link_category'] . '" class="linkcat"><h3>' . $cat['cat_name'] . "</h3>\n\t<ul>\n";
				get_links($cat['link_category'],
					'<li>',"</li>","\n",
					bool_from_yn($cat['show_images']),
					$orderby,
					bool_from_yn($cat['show_description']),
					bool_from_yn($cat['show_rating']),
					$cat['list_limit'],
					bool_from_yn($cat['show_updated']));

				echo "\n\t</ul>\n</li>\n";
			}
		}
	}
}

function blogtxt_widgets_init() {
	if ( !function_exists('register_sidebars') )
		return;

	$p = array(
		'before_title' => "<h3 class='widgettitle'>",
		'after_title' => "</h3>\n",
	);
	register_sidebars(2, $p);

	register_sidebar_widget(__('Search', 'blogtxt'), 'widget_blogtxt_search', null, 'search');
	unregister_widget_control('search');
	register_sidebar_widget(__('Meta', 'blogtxt'), 'widget_blogtxt_meta', null, 'meta');
	unregister_widget_control('meta');
	register_sidebar_widget(__('Links', 'blogtxt'), 'widget_blogtxt_links', null, 'links');
	unregister_widget_control('links');
	register_sidebar_widget(array('Home Link', 'widgets'), 'widget_blogtxt_homelink');
	register_widget_control(array('Home Link', 'widgets'), 'widget_blogtxt_homelink_control', 300, 125);
	register_sidebar_widget(array('RSS Links', 'widgets'), 'widget_blogtxt_rsslinks');
	register_widget_control(array('RSS Links', 'widgets'), 'widget_blogtxt_rsslinks_control', 300, 90);
}

function blogtxt_add_admin() {
	if ( $_GET['page'] == basename(__FILE__) ) {
	
		if ( 'save' == $_REQUEST['action'] ) {

			update_option( 'blogtxt_basefontsize', $_REQUEST['bt_basefontsize'] );
			update_option( 'blogtxt_basefontfamily', $_REQUEST['bt_basefontfamily'] );
			update_option( 'blogtxt_headingfontfamily', $_REQUEST['bt_headingfontfamily'] );
			update_option( 'blogtxt_posttextalignment', $_REQUEST['bt_posttextalignment'] );
			update_option( 'blogtxt_layoutwidth', $_REQUEST['bt_layoutwidth'] );
			update_option( 'blogtxt_authorlink', $_REQUEST['bt_authorlink'] );

			if( isset( $_REQUEST['bt_basefontsize'] ) ) { update_option( 'blogtxt_basefontsize', $_REQUEST['bt_basefontsize']  ); } else { delete_option( 'blogtxt_basefontsize' ); }
			if( isset( $_REQUEST['bt_basefontfamily'] ) ) { update_option( 'blogtxt_basefontfamily', $_REQUEST['bt_basefontfamily']  ); } else { delete_option( 'blogtxt_basefontfamily' ); }
			if( isset( $_REQUEST['bt_headingfontfamily'] ) ) { update_option( 'blogtxt_headingfontfamily', $_REQUEST['bt_headingfontfamily']  ); } else { delete_option('blogtxt_headingfontfamily'); }
			if( isset( $_REQUEST['bt_posttextalignment'] ) ) { update_option( 'blogtxt_posttextalignment', $_REQUEST['bt_posttextalignment']  ); } else { delete_option('blogtxt_posttextalignment'); }
			if( isset( $_REQUEST['bt_layoutwidth'] ) ) { update_option( 'blogtxt_layoutwidth', $_REQUEST['bt_layoutwidth']  ); } else { delete_option('blogtxt_layoutwidth'); }
			if( isset( $_REQUEST['bt_authorlink'] ) ) { update_option( 'blogtxt_authorlink', $_REQUEST['bt_authorlink']  ); } else { delete_option('blogtxt_authorlink'); }

			header("Location: themes.php?page=functions.php&saved=true");
			die;

		} else if ( 'reset' == $_REQUEST['action'] ) {
			delete_option('blogtxt_basefontsize');
			delete_option('blogtxt_basefontfamily');
			delete_option('blogtxt_headingfontfamily');
			delete_option('blogtxt_posttextalignment');
			delete_option('blogtxt_layoutwidth');
			delete_option('blogtxt_authorlink');

			header("Location: themes.php?page=functions.php&reset=true");
			die;
		}
		add_action('admin_head', 'blogtxt_admin_head');
	}
    add_theme_page("blog.txt Options", "blog.txt Options", 'edit_themes', basename(__FILE__), 'blogtxt_admin');
}

function blogtxt_admin_head() {

?>
<meta name="author" content="Scott Allan Wallick" />
<style type="text/css" media="all">
/*<![CDATA[*/
div.wrap table.editform tr td input.radio{background:#fff;border:none;margin-right:3px;}
div.wrap table.editform tr td input.text{text-align:center;width:5em;}
div.wrap table.editform tr td select.dropdown option{margin-right:10px;}
div.wrap table.editform th h3{font:normal 2em/133% georgia,times,serif;margin:1em 0 0.3em;color#222;}
div.wrap table.editform td.important span {background:#f5f5df;padding:0.1em 0.2em;font:85%/175% georgia,times,serif;}
span.info{color:#555;display:block;font-size:90%;margin:3px 0 9px;}
span.info span{font-weight:bold;}
.arial{font-family:arial,helvetica,sans-serif;}
.courier{font-family:'courier new',courier,monospace;}
.georgia{font-family:georgia,times,serif;}
.lucida-console{font-family:'lucida console',monaco,monospace;}
.lucida-unicode{font-family:'lucida sans unicode','lucida grande',sans-serif;}
.tahoma{font-family:tahoma,geneva,sans-serif;}
.times{font-family:'times new roman',times,serif;}
.trebuchet{font-family:'trebuchet ms',helvetica,sans-serif;}
.verdana{font-family:verdana,geneva,sans-serif;}
/*]]>*/
</style>
<?php
}

function blogtxt_admin() {
	if ( $_REQUEST['saved'] ) { ?><div id="message1" class="updated fade"><p><?php printf(__('Blog.txt theme options saved. <a href="%s">View site &raquo;</a>', 'blogtxt'), get_bloginfo('home') . '/'); ?></p></div><?php }
	if ( $_REQUEST['reset'] ) { ?><div id="message2" class="updated fade"><p><?php _e('Blog.txt theme options reset.', 'blogtxt'); ?></p></div><?php } ?>
	
<?php $installedVersion = "3.0"; ?>
<script src="http://www.plaintxt.org/themes/blogtxt-ver-check.php?version=<?php echo $installedVersion; ?>" type="text/javascript"></script>

<div class="wrap" id="blogtxt-options">
	
	<h2><?php _e('Theme Options', 'blogtxt'); ?></h2>
	<p><?php _e('Thanks for selecting the <span class="theme-title">blog.txt</span> theme. You can customize this theme with the options below. <strong>You must click on <i><u>S</u>ave Options</i> to save any changes.</strong> You can also discard your changes and reload the default settings by clicking on <i><u>R</u>eset</i>.', 'blogtxt'); ?></p>

	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
	
		<p class="submit">
			<input name="save" type="submit" value="<?php _e('Save Options &raquo;', 'blogtxt'); ?>" tabindex="26" accesskey="S" />  
			<input name="action" type="hidden" value="save" />
		</p>

		<table class="editform" cellspacing="2" cellpadding="5" width="100%" border="0" summary="blog.txt theme options">

			<tr valign="top">
				<th scope="row" width="33%"><h3><?php _e('Typography', 'blogtxt'); ?></h3></th>
			</tr>

			<tr valign="top">
				<th scope="row" width="33%" style="padding-top:0.7em;"><label for="bt_basefontsize"><?php _e('Base font size', 'blogtxt'); ?></label></th> 
				<td>
					<input id="bt_basefontsize" name="bt_basefontsize" type="text" class="text" value="<?php if ( get_settings('blogtxt_basefontsize') == "" ) { echo "75%"; } else { echo get_settings('blogtxt_basefontsize'); } ?>" tabindex="1" size="10" /><br/>
					<span class="info"><?php _e('The base font size globally affects the size of text throughout your blog. This can be in any unit (e.g., px, pt, em), but I suggest using a percentage (%). Default is <span>75%</span>.', 'blogtxt'); ?></span>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row" width="33%"><?php _e('Base font family', 'blogtxt'); ?></th> 
				<td>
					<label for="bt_basefontArial" class="arial"><input id="bt_basefontArial" name="bt_basefontfamily" type="radio" class="radio" value="arial,helvetica,sans-serif" <?php if ( ( get_settings('blogtxt_basefontfamily') == "") || ( get_settings('blogtxt_basefontfamily') == "arial,helvetica,sans-serif") ) { echo 'checked="checked"'; } ?> tabindex="2" />Arial</label><br/>
					<label for="bt_basefontCourier" class="courier"><input id="bt_basefontCourier" name="bt_basefontfamily" type="radio" class="radio" value="'courier new',courier,monospace" <?php if ( get_settings('blogtxt_basefontfamily') == "\'courier new\',courier,monospace" ) { echo 'checked="checked"'; } ?> tabindex="3" />Courier</label><br/>
					<label for="bt_basefontGeorgia" class="georgia"><input id="bt_basefontGeorgia" name="bt_basefontfamily" type="radio" class="radio" value="georgia,times,serif" <?php if ( get_settings('blogtxt_basefontfamily') == "georgia,times,serif" ) { echo 'checked="checked"'; } ?> tabindex="4" />Georgia</label><br/>
					<label for="bt_basefontLucidaConsole" class="lucida-console"><input id="bt_basefontLucidaConsole" name="bt_basefontfamily" type="radio" class="radio" value="'lucida console',monaco,monospace" <?php if ( get_settings('blogtxt_basefontfamily') == "\'lucida console\',monaco,monospace" ) { echo 'checked="checked"'; } ?> tabindex="5" />Lucida Console</label><br/>
					<label for="bt_basefontLucidaUnicode" class="lucida-unicode"><input id="bt_basefontLucidaUnicode" name="bt_basefontfamily" type="radio" class="radio" value="'lucida sans unicode','lucida grande',sans-serif" <?php if ( get_settings('blogtxt_basefontfamily') == "\'lucida sans unicode\',\'lucida grande\',sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="6" />Lucida Sans Unicode</label><br/>
					<label for="bt_basefontTahoma" class="tahoma"><input id="bt_basefontTahoma" name="bt_basefontfamily" type="radio" class="radio" value="tahoma,geneva,sans-serif" <?php if ( get_settings('blogtxt_basefontfamily') == "tahoma,geneva,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="7" />Tahoma</label><br/>
					<label for="bt_basefontTimes" class="times"><input id="bt_basefontTimes" name="bt_basefontfamily" type="radio" class="radio" value="'times new roman',times,serif" <?php if ( get_settings('blogtxt_basefontfamily') == "\'times new roman\',times,serif" ) { echo 'checked="checked"'; } ?>
					tabindex="8" />Times</label><br/>
					<label for="bt_basefontTrebuchetMS" class="trebuchet"><input id="bt_basefontTrebuchetMS" name="bt_basefontfamily" type="radio" class="radio" value="'trebuchet ms',helvetica,sans-serif" <?php if ( get_settings('blogtxt_basefontfamily') == "\'trebuchet ms\',helvetica,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="9" />Trebuchet MS</label><br/>
					<label for="bt_basefontVerdana" class="verdana"><input id="bt_basefontVerdana" name="bt_basefontfamily" type="radio" class="radio" value="verdana,geneva,sans-serif" <?php if ( get_settings('blogtxt_basefontfamily') == "verdana,geneva,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="10" />Verdana</label><br/>
					<span class="info"><?php printf(__('The base font family sets the font for everything except content headings. The selection is limited to %1$s fonts, as they will display correctly. Default is <span class="arial">Arial</span>.', 'blogtxt'), '<cite><a href="http://en.wikipedia.org/wiki/Web_safe_fonts" title="Web safe fonts - Wikipedia">web safe</a></cite>'); ?></span>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row" width="33%"><?php _e('Heading font family', 'blogtxt'); ?></th> 
				<td>
					<label for="bt_headingfontArial" class="arial"><input id="bt_headingfontArial" name="bt_headingfontfamily" type="radio" class="radio" value="arial,helvetica,sans-serif" <?php if ( get_settings('blogtxt_headingfontfamily') == "arial,helvetica,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="11" />Arial</label><br/>
					<label for="bt_headingfontCourier" class="courier"><input id="bt_headingfontCourier" name="bt_headingfontfamily" type="radio" class="radio" value="'courier new',courier,monospace" <?php if ( get_settings('blogtxt_headingfontfamily') == "\'courier new\',courier,monospace" ) { echo 'checked="checked"'; } ?> tabindex="12" />Courier</label><br/>
					<label for="bt_headingfontGeorgia" class="georgia"><input id="bt_headingfontGeorgia" name="bt_headingfontfamily" type="radio" class="radio" value="georgia,times,serif" <?php if ( ( get_settings('blogtxt_headingfontfamily') == "") || ( get_settings('blogtxt_headingfontfamily') == "georgia,times,serif") ) { echo 'checked="checked"'; } ?> tabindex="13" />Georgia</label><br/>
					<label for="bt_headingfontLucidaConsole" class="lucida-console"><input id="bt_headingfontLucidaConsole" name="bt_headingfontfamily" type="radio" class="radio" value="'lucida console',monaco,monospace" <?php if ( get_settings('blogtxt_headingfontfamily') == "\'lucida console\',monaco,monospace" ) { echo 'checked="checked"'; } ?> tabindex="14" />Lucida Console</label><br/>
					<label for="bt_headingfontLucidaUnicode" class="lucida-unicode"><input id="bt_headingfontLucidaUnicode" name="bt_headingfontfamily" type="radio" class="radio" value="'lucida sans unicode','lucida grande',sans-serif" <?php if ( get_settings('blogtxt_headingfontfamily') == "\'lucida sans unicode\',\'lucida grande\',sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="15" />Lucida Sans Unicode</label><br/>
					<label for="bt_headingfontTahoma" class="tahoma"><input id="bt_headingfontTahoma" name="bt_headingfontfamily" type="radio" class="radio" value="tahoma,geneva,sans-serif" <?php if ( get_settings('blogtxt_headingfontfamily') == "tahoma,geneva,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="16" />Tahoma</label><br/>
					<label for="bt_headingfontTimes" class="times"><input id="bt_headingfontTimes" name="bt_headingfontfamily" type="radio" class="radio" value="'times new roman',times,serif" <?php if ( get_settings('blogtxt_headingfontfamily') == "\'times new roman\',times,serif" ) { echo 'checked="checked"'; } ?> tabindex="17" />Times</label><br/>
					<label for="bt_headingfontTrebuchetMS" class="trebuchet"><input id="bt_headingfontTrebuchetMS" name="bt_headingfontfamily" type="radio" class="radio" value="'trebuchet ms',helvetica,sans-serif" <?php if ( get_settings('blogtxt_headingfontfamily') == "\'trebuchet ms\',helvetica,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="18" />Trebuchet MS</label><br/>
					<label for="bt_headingfontVerdana" class="verdana"><input id="bt_headingfontVerdana" name="bt_headingfontfamily" type="radio" class="radio" value="verdana,geneva,sans-serif" <?php if ( get_settings('blogtxt_headingfontfamily') == "verdana,geneva,sans-serif" ) { echo 'checked="checked"'; } ?> tabindex="19" />Verdana</label><br/>
					<span class="info"><?php printf(__('The heading font family sets the font for all content headings and miscellanea. The selection is limited to %1$s fonts, as they will display correctly. Default is <span class="georgia">Georgia</span>. ', 'blogtxt'), '<cite><a href="http://en.wikipedia.org/wiki/Web_safe_fonts" title="Web safe fonts - Wikipedia">web safe</a></cite>'); ?></span>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row" width="33%"><h3><?php _e('Layout', 'blogtxt'); ?></h3></th>
			</tr>

			<tr valign="top">
				<th scope="row" width="33%" style="padding-top:0.7em;"><label for="bt_layoutwidth"><?php _e('Layout width', 'blogtxt'); ?></label></th> 
				<td>
					<input id="bt_layoutwidth" name="bt_layoutwidth" type="text" class="text" value="<?php if ( get_settings('blogtxt_layoutwidth') == "" ) { echo "auto"; } else { echo get_settings('blogtxt_layoutwidth'); } ?>" tabindex="20" size="10" /><br/>
					<span class="info"><?php _e('The layout width determines the normal width of the entire layout. This can be in any unit (e.g., px, pt, em), but I suggest "auto". Default is <span>auto</span>.', 'blogtxt'); ?></span>
					<span class="info"><?php _e('<em>Note: If you use 100%, the width will be ever-so-larger than the browser window. If you want to play, I suggest playing with ems. But really, auto is the best option.</em>', 'blogtxt'); ?></span>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row" width="33%" style="padding-top:0.7em;"><label for="bt_posttextalignment"><?php _e('Post text alignment', 'blogtxt'); ?></label></th> 
				<td>
					<select id="bt_posttextalignment" name="bt_posttextalignment" tabindex="21" class="dropdown">
						<option value="center" <?php if ( get_settings('blogtxt_posttextalignment') == "center" ) { echo 'selected="selected"'; } ?>><?php _e('Centered', 'blogtxt'); ?> </option>
						<option value="justify" <?php if ( get_settings('blogtxt_posttextalignment') == "justify" ) { echo 'selected="selected"'; } ?>><?php _e('Justified', 'blogtxt'); ?> </option>
						<option value="left" <?php if ( ( get_settings('blogtxt_posttextalignment') == "") || ( get_settings('blogtxt_posttextalignment') == "left") ) { echo 'selected="selected"'; } ?>><?php _e('Left', 'blogtxt'); ?> </option>
						<option value="right" <?php if ( get_settings('blogtxt_posttextalignment') == "right" ) { echo 'selected="selected"'; } ?>><?php _e('Right', 'blogtxt'); ?> </option>
					</select>
					<br/>
					<span class="info"><?php _e('Choose one of the options for the alignment of the post entry text. Default is <span>left</span>.', 'blogtxt'); ?></span>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row" width="33%"><h3><?php _e('Content', 'blogtxt'); ?></h3></th>
			</tr>

			<tr valign="top">
				<th scope="row" width="33%" style="padding-top:0.7em;"><label for="bt_authorlink"><?php _e('Author link', 'blogtxt'); ?></label></th> 
				<td>
					<select id="bt_authorlink" name="bt_authorlink" tabindex="21" class="dropdown">
						<option value="displayed" <?php if ( ( get_settings('blogtxt_authorlink') == "") || ( get_settings('blogtxt_authorlink') == "displayed") ) { echo 'selected="selected"'; } ?>><?php _e('Displayed', 'blogtxt'); ?> </option>
						<option value="hidden" <?php if ( get_settings('blogtxt_authorlink') == "hidden" ) { echo 'selected="selected"'; } ?>><?php _e('Hidden', 'blogtxt'); ?> </option>
					</select>
					<br/>
					<span class="info"><?php _e('The author\'s name and link to his/her corresponding archives page can be displayed or hidden. The "hidden" setting disables the link in an author\'s name in single post footers (and in pages &mdash; see the <a href="#readme">documentation</a> for info). Default is <span>displayed</span>.', 'blogtxt'); ?></span>
				</td>
			</tr>

		</table>

		<p class="submit">
			<input name="save" type="submit" value="<?php _e('Save Options &raquo;', 'blogtxt'); ?>" tabindex="26" accesskey="S" />  
			<input name="action" type="hidden" value="save" />
		</p>

	</form>

	<h2 id="reset"><?php _e('Reset Options', 'blogtxt'); ?></h2>
	<p><?php _e('<strong>Resetting clears all changes to the above options.</strong> After resetting, default options are loaded and this theme will continue to be the active theme. A reset does not affect the actual theme files in any way.', 'blogtxt'); ?></p>

	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<p class="submit">
			<input name="reset" type="submit" value="<?php _e('Reset', 'blogtxt'); ?>" onclick="return confirm('<?php _e('Click OK to reset. Any changes to these theme options will be lost!', 'blogtxt'); ?>');" tabindex="27" accesskey="R" />
			<input name="action" type="hidden" value="reset" />
		</p>
	</form>

</div>

<div id="theme-information" class="wrap">

	<h2 id="info"><?php _e('Theme Information'); ?></h2>
	<p><?php _e('You are currently using the <a href="http://www.plaintxt.org/themes/blogtxt/" title="blog.txt for WordPress"><span class="theme-title">blog.txt</span></a> theme, version ' . $installedVersion . ', by <span class="vcard"><a class="url xfn-me" href="http://scottwallick.com/" title="scottwallick.com" rel="me designer"><span class="n"><span class="given-name">Scott</span> <span class="additional-name">Allan</span> <span class="family-name">Wallick</span></span></a></span>.', 'blogtxt'); ?></p>

	<p><?php printf(__('Please read the included <a href="%1$s" title="Open the readme.html" rel="enclosure" tabindex="28" id="readme">documentation</a> for more information about the <span class="theme-title">blog.txt</span> theme and its advanced features.', 'blogtxt'), get_template_directory_uri() . '/readme.html'); ?></p>

	<h3 id="license" style="margin-bottom:-8px;"><?php _e('License', 'blogtxt'); ?></h3>
	<p><?php printf(__('The <span class="theme-title">blog.txt</span> theme copyright &copy; %1$s by <span class="vcard"><a class="url xfn-me" href="http://scottwallick.com/" title="scottwallick.com" rel="me designer"><span class="n"><span class="given-name">Scott</span> <span class="additional-name">Allan</span> <span class="family-name">Wallick</span></span></a></span> is distributed with the <cite class="vcard"><a class="fn org url" href="http://www.gnu.org/licenses/gpl.html" title="GNU General Public License" rel="license">GNU General Public License</a></cite>.', 'blogtxt'), gmdate('Y') ); ?></p>

</div>

<?php
}

function blogtxt_wp_head() {

	function blogtxt_author_link() {
		global $wpdb, $authordata;
		if ( get_settings('blogtxt_authorlink') == "" ) {
			if ( is_single() || is_page() ) {
				return '<span class="entry-author author vcard"><a class="url fn" href="' . get_author_link(false, $authordata->ID, $authordata->user_nicename) . '" title="View all posts by ' . $authordata->display_name . '">' . get_the_author() . '</a></span>';
			} else {
				echo '<span class="meta-sep">&dagger;</span> <span class="entry-author author vcard"><a class="url fn" href="' . get_author_link(false, $authordata->ID, $authordata->user_nicename) . '" title="View all posts by ' . $authordata->display_name . '">' . get_the_author() . '</a></span>';
			}
		} else if ( get_settings('blogtxt_authorlink') =="displayed" ) {
			if ( is_single() || is_page() ) {
				return '<span class="entry-author author vcard"><a class="url fn" href="' . get_author_link(false, $authordata->ID, $authordata->user_nicename) . '" title="View all posts by ' . $authordata->display_name . '">' . get_the_author() . '</a></span>';
			} else {
				echo '<span class="meta-sep">&dagger;</span> <span class="entry-author author vcard"><a class="url fn" href="' . get_author_link(false, $authordata->ID, $authordata->user_nicename) . '" title="View all posts by ' . $authordata->display_name . '">' . get_the_author() . '</a></span>';
			}
		} else if ( get_settings('blogtxt_authorlink') =="hidden" ) {
			if ( is_single() || is_page() ) {
				return '<span class="entry-author author vcard"><span class="fn n">' . get_the_author() . '</span></span>';
			} else {
				echo '';
			}
		};
	}

	if ( get_settings('blogtxt_basefontsize') == "" ) {
		$basefontsize = '75%';
	} else {
		$basefontsize = stripslashes( get_settings('blogtxt_basefontsize') ); 
	};
	if ( get_settings('blogtxt_basefontfamily') == "" ) {
		$basefontfamily = 'arial,helvtica,sans-serif';
	} else {
		$basefontfamily = stripslashes( get_settings('blogtxt_basefontfamily') ); 
	};
	if ( get_settings('blogtxt_headingfontfamily') == "" ) {
		$headingfontfamily = 'georgia,times,serif';
	} else {
		$headingfontfamily = stripslashes( get_settings('blogtxt_headingfontfamily') ); 
	};
	if ( get_settings('blogtxt_layoutwidth') == "" ) {
		$layoutwidth = 'auto';
	} else {
		$layoutwidth = stripslashes( get_settings('blogtxt_layoutwidth') );
	};
	if ( get_settings('blogtxt_posttextalignment') == "" ) {
		$posttextalignment = 'left';
	} else {
		$posttextalignment = stripslashes( get_settings('blogtxt_posttextalignment') ); 
	};


?>
<style type="text/css" media="all">
/*<![CDATA[*/
/* CSS inserted by theme options */


/*]]>*/
</style>
<?php
}
add_action('admin_menu', 'blogtxt_add_admin');
add_action('wp_head', 'blogtxt_wp_head');

add_action('init', 'blogtxt_widgets_init');
add_filter('archive_meta', 'wptexturize');
add_filter('archive_meta', 'convert_smilies');
add_filter('archive_meta', 'convert_chars');
add_filter('archive_meta', 'wpautop');
?>