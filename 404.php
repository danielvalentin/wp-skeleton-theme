<?php
/*
 * Template name: 404
 */

get_header();

if(have_posts())
{
	while(have_posts())
	{
		the_post();
?>
		<div class="post">
			<h1><?php the_title(); ?></h1>
			<div>
				<?php the_content(); ?>
				
				<div>
<?php
					global $wp_query;
					$term = $wp_query -> query_vars['category_name'];
					if(!empty($wp_query -> query_vars['name']))
					{
						$term = $wp_query -> query_vars['name'];
					}
					$search = search(urldecode($term));
					if(is_array($search) && count($search) > 0)
					{
						echo '<h2>Here\'s a search for "' . $wp_query -> query_vars['name'] . '"</h2>';
						echo '<em>' . count($search) . ' result' . ((count($search) == 1) ? '' : 's') . '</em>';
						echo '<ul>';
						foreach($search as $s)
						{
							echo '<li><a href="' . get_permalink($s -> ID) . '" title="' . $s -> post_title . '">' . $s -> post_title . '</a></li>';
						}
						echo '</ul>';
					}
					else
					{
						echo '<em>Nothing relevant found</em>';
					}
?>
				</div>
			</div>
		</div>
<?php
	}
}

get_footer();