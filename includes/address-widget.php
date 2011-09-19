<?php

class address_Widget extends WP_Widget
{
	
	function address_Widget()
	{
		parent::WP_Widget(false, $name = 'Adresse og område');
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
		
		echo '<div class="address">';
		echo '<div class="left">';
		echo 'Låsbyvej 26<br />';
		echo '2610 Rødovre<br />';
		echo 'Tlf. <strong>36 70 28 99</strong><br />';
		echo '<a href="http://maps.google.dk/maps?q=L%C3%A5sbyvej+26+2610+R%C3%B8dovre&hl=da&ie=UTF8&ll=55.678649,12.447681&spn=0.049265,0.169086&sll=55.869147,11.228027&sspn=6.278895,21.643066&z=13" title="Find Aagaard el på Google maps">&raquo; Se på kort</a>';
		echo '</div>';
		
		echo '<div class="right">';
		echo 'Ørnevej 18<br />';
		echo '4600 Køge<br />';
		echo 'Tlf. <strong>56 66 10 56</strong><br />';
		echo '<a href="http://maps.google.dk/maps?q=%C3%98rnevej+18+4600+K%C3%B8ge&hl=da&ie=UTF8&ll=55.472384,12.171993&spn=0.024762,0.084543&sll=55.678649,12.447681&sspn=0.049265,0.169086&z=14" title="Find Aagaard el på Google maps">&raquo; Se på kort</a>';
		echo '</div>';
		
		echo '</div>';
		echo $after_widget;
	}
	
}
add_action('widgets_init', create_function('', 'return register_widget("address_Widget");'));
