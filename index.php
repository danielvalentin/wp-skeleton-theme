<?php

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
			</div>
		</div>
<?php
	}
}
?>

<div class="pagination">
	
<?php
	echo pagination();
?>
</div>

<?php

get_footer();
