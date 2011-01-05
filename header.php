<!DOCTYPE html>
<head profile="http://gmpg.org/xfn/11">
	
	<meta charset="utf-8" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	
	<link rel="shortcut icon" href="<?php echo get_bloginfo('template_directory'); ?>/media/imgs/favicon.ico" />
	
	<title><?php echo ((!is_home()) ? '&raquo ' . $post -> post_title : ''); ?> &raquo; <?php echo get_bloginfo('title'); ?></title>
	
	<?php wp_head(); ?>
	
</head>
<body>