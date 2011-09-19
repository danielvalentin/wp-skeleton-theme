<?php

function save_delete_widget_rules($x)
{
	if(isset($_POST['delete_widget']) && $_POST['delete_widget'])
	{
		// Add widget
		$id = $_POST['widget-id'];
	}
	elseif(isset($_POST['add_new']) && $_POST['add_new'])
	{
		// Delete widget
		$id = $_POST['widget-id'];
		if(get_option($id.'-rule', false))
		{
			delete_option($id . '-rule');
		}
	}
	else
	{
		// Save widget
		if(isset($_POST['widget-rule']) && !empty($_POST['widget-rule']))
		{
			foreach($_POST['widget-rule'] as $widget_id => $rule)
			{
				$vals = array();
				if($rule == 'only' || $rule == 'never')
				{
					if(is_array($_POST['widget-rule-values']))
					{
						$page_ids = '';
						$i=1;
						foreach($_POST['widget-rule-values'] as $widget_id => $pages)
						{
							foreach($pages as $page_id)
							{
								if($i > 1)
								{
									$page_ids .= ',';
								}
								$page_ids .= $page_id;
								$i++;
							}
						}
						$vals['pages'] = $page_ids;
					}
				}
				$vals['name'] = $rule;
				update_option($widget_id.'-rule', serialize($vals));
			}
		}
	}
}
add_action('widgets.php', 'save_delete_widget_rules');
function add_to_widget_form($widget)
{
	$id = $widget->id;
	$rule = get_option($id . '-rule', false);
	$page_ids = false;
	if($rule)
	{
		if(!is_array($rule))
		{
			$rule = unserialize((string)$rule);
		}
		$page_ids = explode(',',$rule['pages']);
		$rule = $rule['name'];
	}
	echo '<div class="widget-controls">';
	echo '<p>';
	echo '<h3>Vis</h3>';
	echo '<input type="radio"' . (($rule == 'all'||!$rule)?' checked="checked"':'') . ' name="widget-rule[' . $id . ']" id="widget-rule[' . $id . ']-all" value="all" /> <label for="widget-rule[' . $id . ']-all">Overalt</label><br />';
	echo '<input type="radio"' . (($rule == 'only')?' checked="checked"':'') . ' name="widget-rule[' . $id . ']" id="widget-rule[' . $id . ']-only" value="only" /> <label for="widget-rule[' . $id . ']-only">Kun på følgende sider</label><br />';
	echo '<input type="radio"' . (($rule == 'never')?' checked="checked"':'') . ' name="widget-rule[' . $id . ']" id="widget-rule[' . $id . ']-never" value="never" /> <label for="widget-rule[' . $id . ']-never">Aldrig på følgende sider</label><br />';
	echo '</p>';
	echo '<p>';
	echo '<select multiple name="widget-rule-values[' . $id . '][]" style="height:100px;">';
	$pages = get_pages();
	if(is_array($pages)) foreach($pages as $page)
	{
		echo '<option value="' . $page->ID . '"' . (($pages&&in_array($page->ID,$page_ids))?' selected="selected"':'') . '>' . $page->post_title . '</option>';
	}
	echo '</select>';
	echo '</p>';
	echo '</div>';
}
add_action('in_widget_form', 'add_to_widget_form');
function filter_widgets($args)
{
	global $post;
	if(!is_admin())
	{
		foreach($args as $sidebar_id => $values)
		{
			if($sidebar_id != 'wp_inactive_widgets')
			{
				foreach($values as $key => $widget_id)
				{
					$rules = get_option($widget_id . '-rule', false);
					if($rules) // Are rules defined for this widget
					{
						$rules = unserialize($rules);
						if($rules['name'] == 'only')
						{
							$pages = explode(',',$rules['pages']);
							if(!in_array($post->ID, $pages))
							{
								unset($args[$sidebar_id][$key]);
							}
						}
						if($rules['name'] == 'never')
						{
							if(in_array($post->ID, explode(',',$rules['pages'])))
							{
								unset($args[$sidebar_id][$key]);
							}
						}
					}
				}
			}
		}
	}
	return $args;
}
add_filter('sidebars_widgets', 'filter_widgets');