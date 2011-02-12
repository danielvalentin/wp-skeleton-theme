<?php
session_start();

 /**
  * INCLUDES
  */
include('classes/debug.php');

/**
 * Remove unwanted info
 */
function kill_generator()
{
	return '';
}
add_filter('the_generator', 'kill_generator');

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
	wp_enqueue_style('reset', get_bloginfo('template_directory') . '/media/css/reset.css');
	wp_enqueue_style('style', get_bloginfo('template_directory') . '/style.css', array('reset'));
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
		$term = strtolower(strip_tags($_GET['s']));
		return preg_replace('/(' . $term . ')/i', '<strong class="searchterm">$1</strong>', $content);
	}
	add_filter('the_excerpt', 'highlight_searchterm');
	add_filter('the_content', 'highlight_searchterm');
}
// Allow <strong> tags in excerpt (searchterms)
function new_trim_excerpt($text)
{
	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content('');

		$text = strip_shortcodes( $text );

		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text, '<strong>');
		$excerpt_length = apply_filters('excerpt_length', 55);
		$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
		$words = preg_split("/(<a.*?a>)|\n|\r|\t|\s/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . $excerpt_more;
		} else {
			$text = implode(' ', $words);
		}
	}
	return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}
remove_filter('the_excerpt', 'wp_trim_excerpt');
add_filter('the_excerpt', 'new_trim_excerpt');

/**
 * Add "first" and "last" CSS classes to dynamic sidebar widgets. Also adds numeric index class for each widget (widget-1, widget-2, etc.)
 * http://wordpress.org/support/topic/how-to-first-and-last-css-classes-for-sidebar-widgets
 */
function widget_first_last_classes($params) {

	global $my_widget_num; // Global a counter array
	$this_id = $params[0]['id']; // Get the id for the current sidebar we're processing
	$arr_registered_widgets = wp_get_sidebars_widgets(); // Get an array of ALL registered widgets	

	if(!$my_widget_num) {// If the counter array doesn't exist, create it
		$my_widget_num = array();
	}
	if(isset($my_widget_num[$this_id])) { // See if the counter array has an entry for this sidebar
		$my_widget_num[$this_id] ++;
	} else { // If not, create it starting with 1
		$my_widget_num[$this_id] = 1;
	}
	$class = 'class="widget-' . $my_widget_num[$this_id] . ' '; // Add a widget number class for additional styling options
	if($my_widget_num[$this_id] == 1) { // If this is the first widget
		$class .= 'widget-first ';
	}
	if($my_widget_num[$this_id] == count($arr_registered_widgets[$this_id])) { // If this is the last widget
		$class .= 'widget-last ';
	}
	//$params[0]['before_widget'] = str_replace('class="', $class, $params[0]['before_widget']); // Insert our new classes into "before widget"
	$params[0]['before_widget'] = preg_replace('/class=\"/', "$class", $params[0]['before_widget'], 1);
	return $params;
}
add_filter('dynamic_sidebar_params','widget_first_last_classes');

/**
 * Searches for posts - Used on the 404 page
 */
function search($term)
{
	global $wpdb;
	$query = $wpdb -> prepare("SELECT * FROM `wp_posts` WHERE `post_title` LIKE '%s' AND `post_status` = 'publish' AND (`post_type` = 'post' OR `post_type` = 'page')", '%' . $term . '%');
	return $wpdb -> get_results($query);
}

function pagination()
{
	global $wp_query, $paged;
	$total_posts = wp_count_posts() -> publish;
	$current_page = $paged;
	$current_page = (($paged == 0) ? 1 : $paged);
	$posts_per_page = get_option('posts_per_page', true); 
	$num_pages = ceil($total_posts / $posts_per_page);
	$returner = '';
	
	if($current_page > 1)
	{
		$returner .= '<a href="' . get_pagenum_link($current_page - 1) . '" title="Forrige side">&laquo;</a> ';
	}
	
	if($num_pages > 1)
	{
		for($i = 1; $i <= $num_pages; $i++)
		{
			if($current_page != $i)
			{
				$returner .= '<a href="' . get_pagenum_link($i) . '" title="Gå til side ' . $i . '" class="page">' . $i . '</a>';
			}
			else
			{
				$returner .= '<span class="current">' . $i . '</span>';
			}
		}
	}
	if($current_page < $num_pages && $num_pages > 1)
	{
		$returner .= ' <a href="' . get_pagenum_link($current_page + 1) . '" title="Næste side">&raquo;</a>';
	}
	return $returner;
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