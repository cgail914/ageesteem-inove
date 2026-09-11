<?php

/** inove options */
class iNoveOptions {

static function getOptions() {
		$options = get_option('inove_options');
		if (!is_array($options)) {
			$options['google_cse'] = false;
			$options['google_cse_cx'] = '';
			$options['menu_type'] = 'pages';
			$options['nosidebar'] = false;
			$options['notice'] = false;
			$options['notice_content'] = '';
			$options['banner_registered'] = false;
			$options['banner_commentator'] = false;
			$options['banner_visitor'] = false;
			$options['banner_content'] = '';
			$options['showcase_registered'] = false;
			$options['showcase_commentator'] = false;
			$options['showcase_visitor'] = false;
			$options['showcase_caption'] = false;
			$options['showcase_title'] = '';
			$options['showcase_content'] = '';
			$options['author'] = true;
			$options['categories'] = true;
			$options['tags'] = true;
			$options['ctrlentry'] = false;
			$options['feed_readers'] = true;
			$options['feed'] = false;
			$options['feed_url'] = '';
			$options['feed_email'] = false;
			$options['feed_url_email'] = '';
			$options['twitter'] = false;
			$options['twitter_username'] = '';
			$options['analytics'] = false;
			$options['analytics_content'] = '';
			update_option('inove_options', $options);
		}
		return $options;
	}

static function add() {
		if (isset($_POST['inove_save'])) {
			if (!current_user_can('edit_theme_options')) {
				wp_die(esc_html__('You are not allowed to modify theme options.', 'inove'));
			}

			check_admin_referer('inove_save_options', 'inove_options_nonce');

			$options = iNoveOptions::getOptions();
			$post_value = static function ($key, $default = '') {
				if (!isset($_POST[$key]) || !is_string($_POST[$key])) {
					return $default;
				}
				return wp_unslash($_POST[$key]);
			};
			$html_value = static function ($key) use ($post_value) {
				$value = $post_value($key);
				return current_user_can('unfiltered_html') ? $value : wp_kses_post($value);
			};

			foreach (array(
				'google_cse', 'nosidebar', 'notice',
				'banner_registered', 'banner_commentator', 'banner_visitor',
				'showcase_registered', 'showcase_commentator', 'showcase_visitor',
				'showcase_caption', 'author', 'categories', 'tags', 'ctrlentry',
				'feed_readers', 'feed', 'feed_email', 'twitter', 'analytics'
			) as $checkbox) {
				$options[$checkbox] = isset($_POST[$checkbox]);
			}

			$menu_type = sanitize_key($post_value('menu_type', 'pages'));
			$options['menu_type'] = in_array($menu_type, array('pages', 'categories'), true) ? $menu_type : 'pages';
			$options['google_cse_cx'] = sanitize_text_field($post_value('google_cse_cx'));
			$options['notice_content'] = $html_value('notice_content');
			$options['banner_content'] = $html_value('banner_content');
			$options['showcase_title'] = sanitize_text_field($post_value('showcase_title'));
			$options['showcase_content'] = $html_value('showcase_content');
			$options['feed_url'] = esc_url_raw($post_value('feed_url'));
			$options['feed_url_email'] = esc_url_raw($post_value('feed_url_email'));
			$options['twitter_username'] = sanitize_text_field($post_value('twitter_username'));
			$options['analytics_content'] = $html_value('analytics_content');

			update_option('inove_options', $options);
		} else {
			iNoveOptions::getOptions();
		}

		add_theme_page(__('Current Theme Options', 'inove'), __('Current Theme Options', 'inove'), 'edit_theme_options', basename(__FILE__), array('iNoveOptions', 'display'));
	}

static function display() {
		$options = iNoveOptions::getOptions();
?>

<form action="#" method="post" enctype="multipart/form-data" name="inove_form" id="inove_form">
	<?php wp_nonce_field('inove_save_options', 'inove_options_nonce'); ?>
	<div class="wrap">
		<h2><?php _e('Current Theme Options', 'inove'); ?></h2>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e('Search', 'inove'); ?></th>
					<td>
						<label>
							<input name="google_cse" type="checkbox" value="checkbox" <?php if($options['google_cse']) echo "checked='checked'"; ?> />
							 <?php _e('Using google custom search engine.', 'inove'); ?>
						</label>
						<br/>
						<?php _e('CX:', 'inove'); ?>
						 <input type="text" name="google_cse_cx" id="google_cse_cx" class="code" size="40" value="<?php echo esc_attr($options['google_cse_cx']); ?>">
						<br/>
						<?php printf(__('Find <code>name="cx"</code> in the <strong>Search box code</strong> of <a href="%1$s">Google Custom Search Engine</a>, and type the <code>value</code> here.<br/>For example: <code>014782006753236413342:1ltfrybsbz4</code>', 'inove'), 'https://www.google.com/coop/cse/'); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e('Menubar', 'inove'); ?></th>
					<td>
						<label style="margin-right:20px;">
							<input name="menu_type" type="radio" value="pages" <?php if($options['menu_type'] != 'categories') echo "checked='checked'"; ?> />
							 <?php _e('Show pages as menu.', 'inove'); ?>
						</label>
						<label>
							<input name="menu_type" type="radio" value="categories" <?php if($options['menu_type'] == 'categories') echo "checked='checked'"; ?> />
							 <?php _e('Show categories as menu.', 'inove'); ?>
						</label>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e('Sidebar', 'inove'); ?></th>
					<td>
						<label>
							<input name="nosidebar" type="checkbox" value="checkbox" <?php if($options['nosidebar']) echo "checked='checked'"; ?> />
							 <?php _e('Hide sidebar from all pages.', 'inove'); ?>
						</label>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<?php _e('Notice', 'inove'); ?>
						<br/>
						<small style="font-weight:normal;"><?php _e('HTML enabled', 'inove'); ?></small>
					</th>
					<td>
						<!-- notice START -->
						<label>
							<input name="notice" type="checkbox" value="checkbox" <?php if($options['notice']) echo "checked='checked'"; ?> />
							 <?php _e('This notice bar will display at the top of posts on homepage.', 'inove'); ?>
						</label>
						<br />
						<label>
							<textarea name="notice_content" id="notice_content" cols="50" rows="10" style="width:98%;font-size:12px;" class="code"><?php echo esc_textarea($options['notice_content']); ?></textarea>
						</label>
						<!-- notice END -->
					</td>
				</tr>
			</tbody>
		</table>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<?php _e('Banner', 'inove'); ?>
						<br/>
						<small style="font-weight:normal;"><?php _e('HTML enabled', 'inove'); ?></small>
					</th>
					<td>
						<!-- banner START -->
						<?php _e('This banner will display at the right of header. (height: 60 pixels)', 'inove'); ?>
						<br/>
						<?php _e('Who can see?', 'inove'); ?>
						<label style="margin-left:10px;">
							<input name="banner_registered" type="checkbox" value="checkbox" <?php if($options['banner_registered']) echo "checked='checked'"; ?> />
							 <?php _e('Registered Users', 'inove'); ?>
						</label>
						<label style="margin-left:10px;">
							<input name="banner_commentator" type="checkbox" value="checkbox" <?php if($options['banner_commentator']) echo "checked='checked'"; ?> />
							 <?php _e('Commentator', 'inove'); ?>
						</label>
						<label style="margin-left:10px;">
							<input name="banner_visitor" type="checkbox" value="checkbox" <?php if($options['banner_visitor']) echo "checked='checked'"; ?> />
							 <?php _e('Visitors', 'inove'); ?>
						</label>
						<br/>
						<label>
							<textarea name="banner_content" id="banner_content" cols="50" rows="10" style="width:98%;font-size:12px;" class="code"><?php echo esc_textarea($options['banner_content']); ?></textarea>
						</label>
						<!-- banner END -->
					</td>
				</tr>
			</tbody>
		</table>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<?php _e('Showcase', 'inove'); ?>
						<br/>
						<small style="font-weight:normal;"><?php _e('HTML enabled', 'inove'); ?></small>
					</th>
					<td>
						<!-- showcase START -->
						<?php _e('This showcase will display at the top of sidebar.', 'inove'); ?>
						<br/>
						<?php _e('Who can see?', 'inove'); ?>
						<label style="margin-left:10px;">
							<input name="showcase_registered" type="checkbox" value="checkbox" <?php if($options['showcase_registered']) echo "checked='checked'"; ?> />
							 <?php _e('Registered Users', 'inove'); ?>
						</label>
						<label style="margin-left:10px;">
							<input name="showcase_commentator" type="checkbox" value="checkbox" <?php if($options['showcase_commentator']) echo "checked='checked'"; ?> />
							 <?php _e('Commentator', 'inove'); ?>
						</label>
						<label style="margin-left:10px;">
							<input name="showcase_visitor" type="checkbox" value="checkbox" <?php if($options['showcase_visitor']) echo "checked='checked'"; ?> />
							 <?php _e('Visitors', 'inove'); ?>
						</label>
						<br/>
						<label>
							<input name="showcase_caption" type="checkbox" value="checkbox" <?php if($options['showcase_caption']) echo "checked='checked'"; ?> />
							 <?php _e('Title:', 'inove'); ?>
						</label>
						 <input type="text" name="showcase_title" id="showcase_title" class="code" size="40" value="<?php echo esc_attr($options['showcase_title']); ?>" />
						<br/>
						<label>
							<textarea name="showcase_content" id="showcase_content" cols="50" rows="10" style="width:98%;font-size:12px;" class="code"><?php echo esc_textarea($options['showcase_content']); ?></textarea>
						</label>
						<!-- showcase END -->
					</td>
				</tr>
			</tbody>
		</table>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e('Posts', 'inove'); ?></th>
					<td>
						<label style="margin-right:20px;">
							<input name="author" type="checkbox" value="checkbox" <?php if($options['author']) echo "checked='checked'"; ?> />
							 <?php _e('Show author on posts.', 'inove'); ?>
						</label>
						<label style="margin-right:20px;">
							<input name="categories" type="checkbox" value="checkbox" <?php if($options['categories']) echo "checked='checked'"; ?> />
							 <?php _e('Show categories on posts.', 'inove'); ?>
						</label>
						<label>
							<input name="tags" type="checkbox" value="checkbox" <?php if($options['tags']) echo "checked='checked'"; ?> />
							 <?php _e('Show tags on posts.', 'inove'); ?>
						</label>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e('Comments', 'inove'); ?></th>
					<td>
						<label>
							<input name="ctrlentry" type="checkbox" value="checkbox" <?php if($options['ctrlentry']) echo "checked='checked'"; ?> />
							 <?php _e('Submit comments with Ctrl+Enter.', 'inove'); ?>
						</label>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e('Feed', 'inove'); ?></th>
					<td>
						<label>
							<input name="feed_readers" type="checkbox" value="checkbox" <?php if($options['feed_readers']) echo "checked='checked'"; ?> />
							 <?php _e('Show the feed reader list when mouse over on feed button.', 'inove'); ?>
						</label>
						<br />
						<label>
							<input name="feed" type="checkbox" value="checkbox" <?php if($options['feed']) echo "checked='checked'"; ?> />
							 <?php _e('Custom feed.', 'inove'); ?>
						</label>
						 <?php _e('URL:', 'inove'); ?> <input type="text" name="feed_url" id="feed_url" class="code" size="60" value="<?php echo esc_attr($options['feed_url']); ?>">
						<br/>
						<label>
							<input name="feed_email" type="checkbox" value="checkbox" <?php if($options['feed_email']) echo "checked='checked'"; ?> />
							 <?php _e('Email feed.', 'inove'); ?>
						</label>
						 <?php _e('URL:', 'inove'); ?> <input type="text" name="feed_url_email" id="feed_url_email" class="code" size="60" value="<?php echo esc_attr($options['feed_url_email']); ?>">
					</td>
				</tr>
			</tbody>
		</table>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e('Twitter', 'inove'); ?></th>
					<td>
						<label>
							<input name="twitter" type="checkbox" value="checkbox" <?php if($options['twitter']) echo "checked='checked'"; ?> />
							 <?php _e('Add Twitter button.', 'inove'); ?>
						</label>
						<br />
						 <?php _e('Twitter username:', 'inove'); ?>
						 <input type="text" name="twitter_username" id="twitter_username" class="code" size="40" value="<?php echo esc_attr($options['twitter_username']); ?>">
						<br />
						<a href="https://twitter.com/neoease/" onclick="window.open(this.href);return false;">Follow NeoEase</a>
						 | <a href="https://twitter.com/mg12/" onclick="window.open(this.href);return false;">Follow MG12</a>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<?php _e('Web Analytics', 'inove'); ?>
						<br/>
						<small style="font-weight:normal;"><?php _e('HTML enabled', 'inove'); ?></small>
					</th>
					<td>
						<label>
							<input name="analytics" type="checkbox" value="checkbox" <?php if($options['analytics']) echo "checked='checked'"; ?> />
							 <?php _e('Add web analytics code to your site. (e.g. Google Analytics, Yahoo! Web Analytics, ...)', 'inove'); ?>
						</label>
						<label>
							<textarea name="analytics_content" cols="50" rows="10" id="analytics_content" class="code" style="width:98%;font-size:12px;"><?php echo esc_textarea($options['analytics_content']); ?></textarea>
						</label>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input class="button-primary" type="submit" name="inove_save" value="<?php _e('Save Changes', 'inove'); ?>" />
		</p>
	</div>
</form>

<!-- donation -->
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<div class="wrap" style="background:#E3E3E3; margin-bottom:1em;">

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">Donation</th>
					<td>
						If you find my work useful and you want to encourage the development of more free resources, you can do it by donating...
						<br />
						<input type="hidden" name="cmd" value="_s-xclick" />
						<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----" />
						<input style="border:none;" type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif" name="submit" alt="" />
						<img alt="" src="https://www.paypal.com/zh_XC/i/scr/pixel.gif" />
					</td>
				</tr>
			</tbody>
		</table>

	</div>
</form>

<?php
	}
}

// register functions
add_action('admin_menu', array('iNoveOptions', 'add'));


/** l10n */
function theme_init(){
	load_theme_textdomain('inove', get_template_directory() . '/languages');
}
add_action ('init', 'theme_init');

/** Use the classic widget screen for this legacy theme. */
add_filter('use_widgets_block_editor', '__return_false');

/** widgets */
if( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'name' => 'north_sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	));
	register_sidebar(array(
		'name' => 'south_sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	));
	register_sidebar(array(
		'name' => 'west_sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	));
	register_sidebar(array(
		'name' => 'east_sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	));
}

/** Comments */
if (function_exists('wp_list_comments')) {
	// comment count
	function comment_count( $commentcount ) {
		global $id;
		$_comments = get_comments('status=approve&post_id=' . $id);
		$comments_by_type = &separate_comments($_comments);
		return count($comments_by_type['comment']);
	}
}

// custom comments
function custom_comments($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	global $commentcount;
	if(!$commentcount) {
		$commentcount = 0;
	}
?>
	<li class="comment <?php if($comment->comment_author_email == get_the_author_email()) {echo 'admincomment';} else {echo 'regularcomment';} ?>" id="comment-<?php comment_ID() ?>">
		<div class="author">
			<div class="pic">
				<?php if (function_exists('get_avatar') && get_option('show_avatars')) { echo get_avatar($comment, 32); } ?>
			</div>
			<div class="name">
				<?php if (get_comment_author_url()) : ?>
					<a id="commentauthor-<?php comment_ID() ?>" class="url" href="<?php comment_author_url() ?>" rel="external nofollow">
				<?php else : ?>
					<span id="commentauthor-<?php comment_ID() ?>">
				<?php endif; ?>

				<?php comment_author(); ?>

				<?php if(get_comment_author_url()) : ?>
					</a>
				<?php else : ?>
					</span>
				<?php endif; ?>
			</div>
		</div>

		<div class="info">
			<div class="date">
				<?php printf( __('%1$s at %2$s', 'inove'), get_comment_time(__('F jS, Y', 'inove')), get_comment_time(__('H:i', 'inove')) ); ?>
					 | <a href="#comment-<?php comment_ID() ?>"><?php printf('#%1$s', ++$commentcount); ?></a>
			</div>
			<div class="act">
				<a href="javascript:void(0);" onclick="MGJS_CMT.reply('commentauthor-<?php comment_ID() ?>', 'comment-<?php comment_ID() ?>', 'comment');"><?php _e('Reply', 'inove'); ?></a> | 
				<a href="javascript:void(0);" onclick="MGJS_CMT.quote('commentauthor-<?php comment_ID() ?>', 'comment-<?php comment_ID() ?>', 'commentbody-<?php comment_ID() ?>', 'comment');"><?php _e('Quote', 'inove'); ?></a>
				<?php
					if (function_exists("qc_comment_edit_link")) {
						qc_comment_edit_link('', ' | ', '', __('Edit', 'inove'));
					}
					edit_comment_link(__('Advanced edit', 'inove'), ' | ', '');
				?>
			</div>
			<div class="fixed"></div>
			<div class="content">
				<?php if ($comment->comment_approved == '0') : ?>
					<p><small><?php _e('Your comment is awaiting moderation.', 'inove'); ?></small></p>
				<?php endif; ?>

				<div id="commentbody-<?php comment_ID() ?>">
					<?php comment_text(); ?>
				</div>
			</div>
		</div>
		<div class="fixed"></div>

<?php
}
?>
