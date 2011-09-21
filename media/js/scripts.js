/*
 *
 */
$ = jQuery;
$(document).ready(function(){
	
	
	
	/*$('a.zoom').each(function(){
		$(this).attr('title', $(this).attr('title') +  ' <a href="' + $(this).attr('href') + '">Se fuld størrelse</a>');
	}).fancybox({
		
	});*/

});

$.fn.toggleVal = function(val)
{
	$(this).focus(function(){
		if($(this).val() == val)
		{
			$(this).val('');
		}
	});
	$(this).blur(function(){
		if($(this).val() == '')
		{
			$(this).val(val);
		}
	});
}
$.fn.exists = function()
{
	return $(this).length !== 0;
}
$.fn.dontWork = function()
{
	$(this).click(function(){
		return false;
	});
	return $(this);
}

$.fn.scrolls = function() {
	var me = $(this);
	var scrollspeed = 250;
	var initial_top_position = $(me).position().top;
	$(window).scroll(function(){
		var time = setTimeout(function(){
			$(me).stop();
			var viewporttop = $(window).scrollTop();
			if(viewporttop > initial_top_position + 10)
			{
				$(me).animate({
					top:viewporttop - initial_top_position + 10
				}, scrollspeed);
			}
			else
			{
				newpos = viewporttop - initial_top_position - 10;
				if(newpos < initial_top_position)
				{
					newpos = 0;
				}
				$(me).animate({
					top:newpos
				}, scrollspeed);
			}
		}, 100);
	});
}
