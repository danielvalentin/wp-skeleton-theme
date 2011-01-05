<?php
session_start();

/**
 * 
 * TOC:
 *  - Includes
 *  - Fixing æ & ø in slugs
 *  - Adding theme support: post-thumbnails, Menus, Sidebar
 *  - Loading scripts and styles
 *  - Custom comments function
 * 
 *    DISABLED:
 *  - Admin menu and options pages
 *  - Loading widgets
 *  - Custom headers and backgrounds
 * 
 */
 
 /**
  * INCLUDES
  */
include('classes/debug.php');

/**
 * FIX Æ & Ø IN SLUGS
 */
function improved_sanitize_title($title)
{
	$title = str_replace('æ', 'ae', $title);
	$title = str_replace('ø', 'oe', $title);
	$title = str_replace('Æ', 'AE', $title);
	$title = str_replace('Ø', 'OE', $title);
	return $title;
}
add_filter('sanitize_title', 'improved_sanitize_title', 0);

/**
 * ADDING THEME SUPPORT
 */
add_theme_support('post-thumbnails');
register_nav_menus(array('topmenu' => 'Hovedmenuen', 'moremenu' => 'Lille menu over hovedmenu', 'footermenu' => 'Footer menu'));
register_sidebar();

/**
 * LOADING SCRIPTS AND STYLES
 */
function load_styles()
{
	wp_enqueue_style('reset', TEMPLATEPATH . '/media/css/reset.css');
	wp_enqueue_style('style', TEMPLATEPATH . '/style.css', array('reset'));
	//wp_enqueue_style('fancybox', TEMPLATEPATH . '/media/js/fancybox/jquery.fancybox-1.3.1.css');
}
function load_scripts()
{
	wp_deregister_script('jquery');
	wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js', array(), '1.4.2');
	//wp_register_script('jqueryswfobject', get_bloginfo('template_directory') . '/media/js/jquery.swfobject.js', array('jquery'));
	//wp_register_script('cufon', get_bloginfo('template_directory') . '/media/js/cufon.js');
	wp_register_script('scripts', get_bloginfo('template_directory') . '/media/js/scripts.js', array('jquery'));
	//wp_register_script('fancybox', get_bloginfo('template_directory') . '/media/libs/fancybox/jquery.fancybox-1.3.1.pack.js', array('jquery'));
	wp_print_scripts(array('jquery', 'scripts'));
}
if(!is_admin())
{
	add_action('init', 'load_styles');
	add_action('wp_footer', 'load_scripts');
}

/**
 * CUSTOM COMMENT FUNCTION
 */
function theme_comments($comment, $args, $depth)
{
	$GLOBALS['comment'] = $comment;
	$args['reply_text '] = 'Svar';
	switch($comment -> comment_type)
	{
		case '':
?>
			<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
				<div id="comment-<?php comment_ID(); ?>">
				<div class="comment-author vcard">
					<?php echo get_avatar( $comment, 40 ); ?>
					<?php printf('%s <span class="says">siger:</span>', sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
				</div><!-- .comment-author .vcard -->
				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em>Din kommentar afventer godkendelse.</em>
					<br />
				<?php endif; ?>

				<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
					<?php
						/* translators: 1: date, 2: time */
						printf('%1$s kl. %2$s', get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link('(Rediger)', ' ' );
					?>
				</div><!-- .comment-meta .commentmetadata -->

				<div class="comment-body"><?php comment_text(); ?></div>

				<div class="reply">
					<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				</div>
			</div>
<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
			<li class="post pingback">
				<p><?php _e( 'Pingback:', 'twentyten' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'twentyten'), ' ' ); ?></p>
	<?php
			break;
	}
}

// Highlight searchterms
if($_GET['s'])
{
	function highlight_searchterm($content)
	{
		$term = strip_tags($_GET['s']);
		return str_replace($term, '<strong class="searchterm">' . $term . '</strong>', $content);
	}
	add_filter('the_excerpt', 'highlight_searchterm');
	add_filter('the_content', 'highlight_searchterm');
}

/**
 * DISABLED BY DEFAULT
 */

 /**
  * ADMIN OPTIONS PAGES
  */
/*function add_admin_option_pages()
{
	add_menu_page('Kurser', 'Kurser', 'edit_posts', 'kursus_options', 'add_admin_options');
	add_submenu_page('nyhedsbrev', 'Nyhedsbrev indstillinger', 'Opsætning', 'edit_posts', 'nyhedsbrev-options', 'add_admin_submenu_options');
}
add_action('admin_menu', 'add_admin_option_pages');
function add_admin_options()
{
	include('plugins/name/index.php');
}
function add_admin_submenu_options()
{
	include('plugins/name/sub/index.php');
}*/

/**
 * LOADING WIDGETS
 */
//include('plugins/widgets/latest.php');
//add_action('widgets_init', create_function('', 'return register_widget("latest_Widget");'));

/** 
 * CUSTOM HEADERS AND BACKGROUNDS
 */
/*
define('HEADER_TEXTCOLOR', '000');
define('HEADER_IMAGE', '%s/media/imgs/logo.png');
define('HEADER_IMAGE_WIDTH', 140);
define('HEADER_IMAGE_HEIGHT', 37);
// Custom header BG
add_custom_image_header('header_style', 'admin_header_style');
function header_style()
{?>
	<style type="text/css">
		#logo{background:url(<?php header_image(); ?>);}
	</style>
<?php
}
function admin_header_style()
{
	echo '<style type="text/css">';
	echo '#headimg{width:' . HEADER_IMAGE_WIDTH . 'px;height:' . HEADER_IMAGE_HEIGHT . 'px;}';
	echo '</style>';
}
// Custom BG
add_custom_background('background_style');
function background_style()
{
?>
	<style type="text/css">
		body{background:#<?php echo get_background_color(); ?> url(<?php echo get_background_image() ?>) <?php echo get_theme_mod( 'background_repeat', 'repeat' ); ?>;}
	</style>
<?php
}*/