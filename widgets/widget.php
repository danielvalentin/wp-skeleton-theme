<?php

class Mywidget_Widget extends WP_Widget
{
	
	function Mywidget_Widget()
	{
		parent::WP_Widget(false, $name = 'Mywidget');
	}
	
	function form($instance)
	{
		$title = esc_attr($instance['title']);
		echo '<p><label for="' . $this -> get_field_id('title') . '">Overskrift</label> <input type="text" class="widefat" id="' . $this -> get_field_id('title') . '" name="' . $this -> get_field_name('title') . '" value="' . $title . '" /></p>';
	}
	
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}
	
	function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		echo $before_title;
		echo (($title) ? $title : 'Mywidget');
		
		echo $after_title;
		
		echo '<div id="mywidget">';
		
		echo '</div>';
		echo $after_widget;
	}
	
}