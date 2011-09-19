<?php
session_start();

 /**
  * INCLUDES
  */
include('classes/debug.php');
include('classes/mobile.php');
include('includes/functions.php');
include('includes/fixes.php');
include('includes/improvements.php');
include('includes/widget-page-filtering.php');
include('includes/seo/seo.php');
include('includes/address-widget.php');
include('includes/spacer-widget.php');
include('includes/frontpage-slideshow.php');

/**
 * ADDING THEME SUPPORT
 */
add_theme_support('post-thumbnails');
set_post_thumbnail_size(100, 100, true);
register_nav_menus(array('mainmenu' => 'Main menu'));
register_sidebar(array('name' => 'Sidebar', 'description' => 'Main sidebar'));

/**
 * LOADING SCRIPTS AND STYLES
 */
function load_styles()
{
	wp_enqueue_style('reset', get_bloginfo('template_directory') . '/media/css/reset.css');
	wp_enqueue_style('style', get_bloginfo('template_directory') . '/style.css', array('reset'));
}
function load_scripts()
{
	wp_deregister_script('jquery');
	wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js', array(), '1.4.2');
	wp_register_script('scripts', get_bloginfo('template_directory') . '/media/js/scripts.js', array('jquery'));
	echo '<script type="text/javascript">';
	echo 'var url = "' . get_bloginfo('url') . '";';
	echo '</script>';
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
				<p>Pingback <?php comment_author_link(); ?><?php edit_comment_link( 'Rediger', ' ' ); ?></p>
	<?php
			break;
	}
}

/**
 * DISABLED BY DEFAULT
 */

 /**
 * Mobile detection
 */
 /*$detection = new mobile();
 if(false&&$detection -> isMobile())
 {
 	function switch_to_mobile_templates($dir)
	{
		$template = dirname(__FILE__) . '/mobile/index.php';
		if(is_page_template('contact.php'))
		{
			$template = dirname(__FILE__) . '/mobile/contact.php';
		}
		return $template;
	}
	add_filter('template_include', 'switch_to_mobile_templates');
}*/

/**
 * Add widget
 */
/*include('widgets/widget.php');
add_action('widgets_init', create_function('', 'return register_widget("Mywidget_Widget");'));

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