<?php

class Spacer_Widget extends WP_Widget
{
	
	function Spacer_Widget()
	{
		parent::WP_Widget(false, $name = 'Fylder', array('description' => 'Tom "fylde" widget til footeren hvis der ønskes luft.'));
	}
	
	function widget($args, $instance)
	{
		echo $before_widget;
		
		echo '<li class="widget spacer"></li>';
		echo $after_widget;
	}
	
}

add_action('widgets_init', create_function('', 'return register_widget("Spacer_Widget");'));
