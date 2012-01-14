<!DOCTYPE html>
<html>
<head profile="http://gmpg.org/xfn/11">
	
	<meta charset="utf-8" />
	<meta charset="utf-8" />
	<meta name="description" content="<?php echo get_seo_description($post->ID); ?>" />
	<meta name="keywords" content="<?php echo get_seo_keywords($post->ID); ?>" />
	
	<link rel="shortcut icon" href="<?php echo get_bloginfo('template_directory'); ?>/media/imgs/favicon.ico" />
	
	<title><?php echo get_seo_title($post->ID); ?></title>
	
	<?php wp_head(); ?>
	
</head>
<body>
