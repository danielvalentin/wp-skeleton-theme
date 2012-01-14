<?php

// Metaboxes
function seo_meta_box($post)
{
	$blogtitle = get_bloginfo('name');
	wp_nonce_field(plugin_basename(__FILE__), 'seo_noncename');
	echo '<table class="widefat"><tr><td>';
	echo '<label for="seo-title">Sidetitel</label>';
	echo '</td><td>';
	$title = get_option('post-' . $post->ID . '-seo-title', '');
	echo '<input type="hidden" id="seo-site-title" value=" | ' . $blogtitle . '" />';
	echo '<input type="text" name="seo-title" id="seo-title" value="' . $title . '" style="width:95%;" /> <span id="seo-charcount">' . (70 - strlen($blogtitle) - strlen($title)) . '</span>';
	echo '</td></tr><tr><td style="vertical-align:top;">';
	echo '<label for="seo-description">Meta beskrivelse</label>';
	echo '</td><td>';
	echo '<textarea name="seo-description" id="seo-description" style="width:95%;">' . get_option('post-' . $post->ID . '-seo-description', '') . '</textarea>';
	echo '</td></tr><tr><td style="vertical-align:top;">';
	echo '<label for="seo-keywords">Meta keywords</label>';
	echo '</td><td>';
	echo '<textarea name="seo-keywords" id="seo-keywords" style="width:95%;">' . get_option('post-' . $post->ID . '-seo-keywords', '') . '</textarea>';
	echo '</td></tr></table><br /><em>Lad et felt være tomt for at generere automatisk</em>';
}
function load_admin_seo_js()
{
	wp_register_script('admin-seo', get_bloginfo('template_directory') . '/includes/seo/js/admin-seo.js', array('jquery'));
	wp_print_scripts(array('admin-seo'));
}
add_action('admin_print_scripts-post.php', 'load_admin_seo_js');
add_action('admin_print_scripts-post-new.php', 'load_admin_seo_js');
function add_seo_meta_box()
{
	add_meta_box('seobox', 'SEO', 'seo_meta_box', 'page', 'normal', 'high');
	add_meta_box('seobox', 'SEO', 'seo_meta_box', 'post', 'normal', 'high');
}
function save_seo_stuff($post_id)
{
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	{
		return;
	}
	if(!wp_verify_nonce($_POST['seo_noncename'], plugin_basename(__FILE__)))
	{
		return;
	}
	if(!current_user_can('edit_post', $post_id))
	{
		return;
	}
	update_option('post-' . $post_id . '-seo-title', $_POST['seo-title']);
	update_option('post-' . $post_id . '-seo-description', $_POST['seo-description']);
	update_option('post-' . $post_id . '-seo-keywords', $_POST['seo-keywords']);
}
add_action('admin_init', 'add_seo_meta_box');
add_action('save_post', 'save_seo_stuff');
function delete_seo_stuff($post_id)
{
	delete_option('post-' . $post_id . 'seo-title');
	delete_option('post-' . $post_id . 'seo-description');
	delete_option('post-' . $post_id . 'seo-keywords');
}
add_filter('delete_post', 'delete_seo_stuff', 10);
// SEO API
function get_seo_title($post_id)
{
	$title = get_option('post-' . $post_id . '-seo-title', false);
	if(!$title)
	{
		$post = get_post($post_id);
		$title = $post->post_title;
	}
	if(is_404())
	{
		$page = error404();
		$title = $page -> post_title;
	}
	return $title . ' | ' . get_bloginfo('name');
}
function get_seo_description($post_id)
{
	$desc = get_option('post-' . $post_id . '-seo-description', false);
	if($desc)
	{
		return $desc;
	}
	else
	{
		$post = get_post($post_id);
		$excerpt = $post->post_excerpt;
		$raw_excerpt = $excerpt;
		if($excerpt == '')
		{
			$content = $post->post_content;
			$content = strip_shortcodes($content);
			$content = apply_filters('the_content', $content);
			$content = str_replace(']]>', ']]&gt;', $content);
			$content = str_replace(array("\t","\n","\r\n", "\r"), ' ', $content);
			$content = str_replace(array('"', '\''), '', $content);
			$content = strip_tags($content);
			$excerpt = substr($content, 0, 155);
			if(strlen($content) > 155)
			{
				$excerpt .= ' [...]';
			}
		}
		return apply_filters('wp_trim_excerpt', $excerpt, $raw_excerpt);
	}
}
function get_seo_keywords($post_id)
{
	$keywords = get_option('post-' . $post_id . '-seo-keywords', false);
	if($keywords)
	{
		return $keywords;
	}
	else
	{
		//$post = get_post($post_id);
		$keywords = '';
		$tags = get_the_tags($post_id);
		if(is_array($tags)) foreach($tags as $tag)
		{
			$keywords .= $tag . ',';
		}
		$cats = wp_get_post_categories($post_id, array('fields' => 'names'));
		if(is_array($cats)) foreach($cats as $cat)
		{
			$keywords .= $cats . ',';
		}
		return substr($keywords, 0, strlen($keywords) - 1);
	}
}
/**
 * XML sitemap
 */
function get_change_frequency($timestamp)
{
	if(($timestamp/86400) < 7) // Less than 1 week old
	{
		return 'daily';
	}
	elseif(($timestams/604800) < 12) // Between 1 and 12 weeks
	{
		return 'weekly';
	}
	elseif(($timestamp/604800)<52) // Between 12 and 52 weeks
	{
		return 'monthly';
	}
	else
	{
		return 'yearly';
	}
}
function create_sitemap()
{
	$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
	foreach(get_pages() as $page)
	{
		$date = strtotime($page->post_modified);
		$xml .= '<url>';
		$xml .= '<loc>' . get_permalink($page->ID) . '</loc>';
		$xml .= '<lastmod>' . date('Y-m-d', $date) . '</lastmod>';
		$xml .= '<changefreq>' . get_change_frequency($date) . '</changefreq>';
		$xml .='</url>';
	}
	foreach(get_posts() as $post)
	{
		$date = strtotime($post->post_modified);
		$xml .= '<url>' . "\n";
		$xml .= '<loc>' . get_permalink($post->ID) . '</loc>';
		$xml .= '<lastmod>' . date('Y-m-d', $date) . '</lastmod>';
		$xml .= '<changefreq>' . get_change_frequency($date) . '</changefreq>';
		$xml .='</url>';
	}
	$xml .= '</urlset>';
	file_put_contents(ABSPATH . '/sitemap.xml',$xml);
}
add_action('save_post', 'create_sitemap');
