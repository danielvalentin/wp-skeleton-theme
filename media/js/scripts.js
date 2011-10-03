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
/**
 * http://phpjs.org/functions/number_format:481
 */
function number_format (number, decimals, dec_point, thousands_sep)
{
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
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
