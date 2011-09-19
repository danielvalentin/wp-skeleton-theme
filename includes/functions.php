<?php

/**
 * Get WP root dir
 */
function get_wp_config_path()
{
    $base = dirname(__FILE__);
    $path = false;

    if (@file_exists(dirname(dirname($base))."/wp-config.php"))
    {
        $path = dirname(dirname($base))."/wp-config.php";
    }
    else
    if (@file_exists(dirname(dirname(dirname($base)))."/wp-config.php"))
    {
        $path = dirname(dirname(dirname($base)))."/wp-config.php";
    }
    else
    $path = false;

    if ($path != false)
    {
        $path = str_replace("\\", "/", $path);
    }
    return $path;
}

/**
 * Searches for posts - Used on the 404 page
 */
function search($term)
{
	global $wpdb;
	$query = $wpdb -> prepare("SELECT * FROM `wp_posts` WHERE `post_title` LIKE '%s' AND `post_status` = 'publish' AND (`post_type` = 'post' OR `post_type` = 'page') AND `post_name` != 'fejl404'", '%' . $term . '%');
	return $wpdb -> get_results($query);
}

/**
 * Pagination
 */
function pagination()
{
	$returner = '';
	if(!is_single())
	{
		global $wp_query, $paged;
		$total_posts = $wp_query -> found_posts;//wp_count_posts() -> publish;
		$current_page = $paged;
		$current_page = (($paged == 0) ? 1 : $paged);
		$posts_per_page = get_option('posts_per_page', true); 
		$num_pages = ceil($total_posts / $posts_per_page);
		
		if($current_page > 1)
		{
			$returner .= '<a href="' . get_pagenum_link($current_page - 1) . '" title="Forrige side af ' . $wp_query->query_vars['category_name'] . '" class="page">&laquo;</a> ';
		}
		
		if($num_pages > 1)
		{
			for($i = 1; $i <= $num_pages; $i++)
			{
				if($current_page != $i)
				{
					$returner .= '<a href="' . get_pagenum_link($i) . '" title="Gå til side ' . $i . ' af ' . $wp_query->query_vars['category_name'] . '" class="page">' . $i . '</a>';
				}
				else
				{
					$returner .= '<span class="page current">' . $i . '</span>';
				}
			}
		}
		if($current_page < $num_pages && $num_pages > 1)
		{
			$returner .= ' <a href="' . get_pagenum_link($current_page + 1) . '" title="Næste side af ' . $wp_query->query_vars['category_name'] . '" class="page">&raquo;</a>';
		}
	}
	if(!empty($returner))
	{
		$returner .= '<div class="explain">Gå til side</div>';
	}
	return $returner;
}

/**
 * Breadcrumbs
 */
function breadcrumbs($post_id, $seperator = '&raquo;')
{
	$crumbs = array();
	if(!is_home() && !is_front_page())
	{
		$post = get_post($post_id);
		if(is_page($post_id))
		{
			$crumbs[] = $seperator . ' ' . $post -> post_title;
			while($post -> post_parent != 0)
			{
				$post = get_post($post -> post_parent);
				$crumbs[] = $seperator . ' ' . '<a href="' . get_permalink($post -> ID) . '">' . $post -> post_title . '</a>';
			}
		}
		elseif(is_tag())
		{
			$crumbs[] = $seperator . ' ' . single_tag_title('', false);
		}
		elseif(is_post_type_archive())
		{
			$crumbs[] = $seperator . ' ' . get_post_type($post);
		}
		elseif(is_category() && !is_post_type_archive())
		{
			$crumbs[] = $seperator . ' ' . single_cat_title('', false);
			$cat = get_category(get_query_var('cat'));
			while($cat->parent != 0)
			{
				$cat = get_category($cat -> parent);
				$crumbs[] = $seperator . ' <a href="' . get_category_link($cat -> term_id) . '" title="' . $cat -> name . '">' . $cat -> name . '</a>';
			}
		}
		elseif(is_search())
		{
			$crumbs[] = $seperator . ' Søgning på "' . get_query_var('s') . '"';
		}
		elseif(is_404())
		{
			$crumbs[] = $seperator . ' 404';
		}
		else
		{
			$crumbs[] = $seperator . ' ' . $post -> post_title;
			$cats = get_the_category();
			$cat = get_category($cats[0]->term_id);
			$crumbs[] = $seperator . ' <a href="' . get_category_link($cat -> term_id) . '" title="' . $cat -> name . '">' . $cat -> name . '</a>';
			while($cat->parent != 0)
			{
				$cat = get_category($cat -> parent);
				$crumbs[] = $seperator . ' <a href="' . get_category_link($cat -> term_id) . '" title="' . $cat -> name . '">' . $cat -> name . '</a>';
			}
		}
		$crumbs[] = '<a href="' . get_bloginfo('url') . '" title="Forside">Forside</a>';
	}
	$crumbs = implode(' ', array_reverse($crumbs));
	return $crumbs;
}

/**
 * Fetch the page thats titled "footer" (case insensitive)
 */
function footer($default = '')
{
	global $wpdb;
	$content = $wpdb -> get_var("SELECT `post_content` FROM $wpdb->posts WHERE LOWER(`post_title`) = 'footer' AND `post_type` = 'page' AND `post_status` = 'publish' LIMIT 1");
	if($content)
	{
		return apply_filters('the_content', $content); 
	}
	return $default;
}
/**
 * Fetch the page thats titled "404"
 */
function error404()
{
	global $wpdb;
	$content = $wpdb -> get_row("SELECT * FROM $wpdb->posts WHERE LOWER(`post_name`) = 'fejl404' AND `post_type` = 'page' AND `post_status` = 'publish' LIMIT 1");
	if($content)
	{
		$content -> post_title = apply_filters('the_title', $content -> post_title);
		$content -> post_content = apply_filters('the_content', $content -> post_content);
		return $content;
	}
	else
	{
		$content = (object)array();
		$content -> post_title = 'Fejl! Siden blev ikke fundet!';
		$content -> post_content = 'Siden du leder efter blev ikke fundet! Dette kan skyldes at indholdet er blevet flyttet til en anden adresse, eller at der er fejl i URL\'en.';
		return $content;
	}
}
