jQuery(document).ready(function(){
	if(jQuery('#seo-charcount').length !== 0)
	{
		jQuery('#seo-charcount').html(70-jQuery('#seo-site-title').val().length-jQuery('#seo-title').val().length);
		if(jQuery('#seo-title').val().length > (70 - jQuery('#seo-site-title').val().length) && !jQuery('#seo-charcount').hasClass('error'))
		{
			jQuery('#seo-charcount').css({
				color:'red'
			});
		}
		jQuery('#seo-title').keyup(function(){
			var length = jQuery('#seo-title').val().length;
			if(length > (70 - jQuery('#seo-site-title').val().length) && !jQuery('#seo-charcount').hasClass('error'))
			{
				jQuery('#seo-charcount').css({
					color:'red'
				});
			}
			else
			{
				jQuery('#seo-charcount').css({
					color:'#555'
				});
			}
			jQuery('#seo-charcount').html(70-jQuery('#seo-site-title').val().length-length);
		});
	}
});
