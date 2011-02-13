<?php
get_header();

$page = error404();

?>
<div class="post">
	<h1><?php echo apply_filters('the_title', $page -> post_title); ?></h1>
	<div>
		<?php echo apply_filters('the_content', $page -> post_content); ?>
		
		<div>
			<h3>Her er en søgning på siden efter lignende indhold:</h3>
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
				echo '<em>' . count($search) . ' resultat' . ((count($search) == 1) ? '' : 'er') . '</em>';
				echo '<ul>';
				foreach($search as $s)
				{
					echo '<li><a href="' . get_permalink($s -> ID) . '" title="' . $s -> post_title . '">' . $s -> post_title . '</a></li>';
				}
				echo '</ul>';
			}
			else
			{
				echo '<em>Intet relevant fundet</em>';
			}
?>
		</div>
	</div>
</div>
<?php

get_footer();