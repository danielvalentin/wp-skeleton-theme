<?php

/**
 * Remove unwanted header info
 */
function kill_generator()
{
	return '';
}
add_filter('the_generator', 'kill_generator');
add_action('wp_head', 'remove_widget_action', 1); // Recent comments widget CSS
function remove_widget_action() {
    global $wp_widget_factory;

    remove_action( 'wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style') );
}

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
 * Adding a home link to the menus
 */
function add_home_links($args)
{
	$args['show_home'] = true;
	return $args;
}
add_filter('wp_page_menu_args', 'add_home_links');

