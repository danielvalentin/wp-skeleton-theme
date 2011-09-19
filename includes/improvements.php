<?php

/**
 * Highlight searchterms
 */
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
 * Camouflage emails in pages and posts
 * Thanks Kohana (kohanaframework.org)
 */
function camouflage_emails($content)
{
	$content = preg_replace_callback('/\b([A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4})\b/i', 'obfuscate_email', $content);
	return $content;
}
add_filter('the_content', 'camouflage_emails');
function obfuscate_email($string)
{
	$string = $string[0];
	$safe = '';
	foreach (str_split($string) as $letter)
	{
		switch (rand(1, 3))
		{
			// HTML entity code
			case 1: $safe .= '&#'.ord($letter).';'; break;
			// Hex character code
			case 2: $safe .= '&#x'.dechex(ord($letter)).';'; break;
			// Raw (no) encoding
			case 3: $safe .= $letter;
		}
	}
	return str_replace('@', '&#64;', $safe);
}

/**
 * Add tinymce to categories description
 */
function tinymce_category_options($vars)
{
	$vars['theme'] = 'advanced';
	$vars['skin'] = 'wp_theme';
	$vars['height'] = 300;
	$vars['width'] = 440;
	$vars['onpageload'] = '';
	$vars['mode'] = 'exact';
	$vars['elements'] = 'tag-description';
	if(isset($_GET['tag_ID']) && !empty($_GET['tag_ID']))
	{
		$vars['elements'] .= ',description';
	}
	$vars['theme_advanced_buttons1'] = 'formatselect, bold, italic, pastetext, pasteword, bullist, numlist, link, unlink, outdent, indent, charmap, removeformat, spellchecker, fullscreen, wp_help';
	$vars['theme_advanced_blockformats'] = 'p,h2,h3,h4,h5,h6';
	$vars['theme_advanced_disable'] = 'strikethrough,underline,forecolor,justifyfull';
	return $vars;
}
function add_tinymce_to_categories()
{
	if ( basename($_SERVER['PHP_SELF']) == 'edit-tags.php' && function_exists('wp_tiny_mce') && $_GET['taxonomy'] == 'category' )
	{
		wp_admin_css();
		wp_enqueue_script('utils');
		wp_enqueue_script('editor');
		do_action('admin_print_scripts');
		do_action('admin_print_styles-post-php');
		do_action('admin_print_styles');
		remove_all_filters('mce_external_plugins');
		add_filter('teeny_mce_before_init', 'tinymce_category_options');
		wp_tiny_mce(true);
	}
}
add_action('admin_head', 'add_tinymce_to_categories');
