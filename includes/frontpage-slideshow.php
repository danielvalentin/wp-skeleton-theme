<?php
 
function add_slideshow_metabox()
{
	add_meta_box('slideshowbox', 'Slideshow', 'slideshow_metabox_content', 'page', 'normal', 'low');
	add_meta_box('slideshowbox', 'Slideshow', 'slideshow_metabox_content', 'post', 'normal', 'low');
	add_meta_box('slideshowbox', 'Slideshow', 'slideshow_metabox_content', 'reference', 'normal', 'low');
}
add_action('admin_init', 'add_slideshow_metabox');
function slideshow_metabox_content($post)
{
	$vals = get_option('slideshow', false);
	$text = '';
	$is_added = false;
	if($vals)
	{
		$vals = maybe_unserialize($vals);
		if(key_exists($post->ID, $vals))
		{
			$is_added = true;
			$text = $vals[$post->ID];
		}
	}
	wp_nonce_field(plugin_basename(__FILE__), 'slideshow_noncename');
	echo '<table class="widefat"><tr><td colspan="2">';
	echo '<select name="slideshow-option">';
	echo '<option value="off"' . ($is_added?'':' selected="selected"') . '>Vis ikke i slideshow</option>';
	echo '<option value="on"' . ($is_added?' selected="selected"':'') . '>Vis i slideshow</option>';
	echo '</select>';
	echo '</td></tr>';
	echo '<tr><td><label for="slideshow-description">Beskrivelse</label></td></tr>';
	echo '<tr><td><textarea style="width:95%;" name="slideshow-description" id="slideshow-description">' . $text . '</textarea></td></tr>';
	echo '<tr><td colspan="2">';
	echo '<strong>Andre slides (' . count($vals) . ' ialt):</strong><br />';
	echo '<ol>';
	foreach($vals as $slide => $vals)
	{
		if($slide != $post->ID)
		{
			$page = get_page($slide);
			echo '<li><a href="' . admin_url('post.php?post=' . $slide . '&action=edit') . '">' . $page->post_title . '</a></li>';
		}
	}
	echo '</ol>';
	echo '</td></tr>';
	echo '</table>';
}
function slideshow_metabox_save($post_id)
{
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	{
		return;
	}
	if(!wp_verify_nonce($_POST['slideshow_noncename'], plugin_basename(__FILE__)))
	{
		return;
	}
	if(!current_user_can('edit_post', $post_id))
	{
		return;
	}
	if(isset($_POST['slideshow-option']))
	{
		$post = get_post($post_id);
		if($post -> post_type != 'revision')
		{
			$vals = get_option('slideshow', false);
			if(!$vals)
			{
				$vals = array();
			}
			else
			{
				$vals = maybe_unserialize($vals);
			}
			//die(var_dump($_POST));
			if($_POST['slideshow-option'] == 'on')
			{ // Add
				$vals[$post_id] = $_POST['slideshow-description'];
			}
			elseif($_POST['slideshow-option'] == 'off')
			{ // Remove
				unset($vals[$post_id]);
			}
			update_option('slideshow', serialize($vals));
		}
	}
}
add_action('save_post', 'slideshow_metabox_save');
