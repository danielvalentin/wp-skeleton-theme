/*
 *
 */
$ = jQuery;
$(document).ready(function(){
	
	$('.flash').each(function(){
		$(this).attr('style', '').flash({
			swf:flvplayerpath,
			width:$(this).attr('width'),
			height:$(this).attr('height'),
			allowFullScreen:true,
			flashvars:{
				screenshot:$(this).attr('screenshot'),
				videoPath:$(this).attr('flv')
			}
		});
	});
	
	$('a.zoom').each(function(){
		$(this).attr('title', $(this).attr('title') +  ' <a href="' + $(this).attr('href') + '">Se fuld størrelse</a>');
	}).fancybox({
		
	});

	// Slideshow
	var imgs = $('#slideshow > img');
	var currentImg = 0;
	setInterval(function(){
		$(imgs[currentImg]).fadeOut();
		currentImg++;
		if(currentImg + 1 > imgs.length) currentImg = 0;
		$(imgs[currentImg]).fadeIn();
	}, 5000);

	// Fonts
	Cufon.replace('.cufon-replace');
	Cufon.replace('#menu-topmenu li a', {hover:true});
	Cufon.replace('#sidebar li h2');

	// Dropdown menus
	$('#menu-topmenu li a').each(function(){
		$(this).mouseover(function(){
			if($(this).next('.sub-menu') != null)
			{
				var p = $(this).parent().position();
				$(this).next('.sub-menu').css({
					left:p.left + 'px',
					top:(p.top + $(this).height() ) + 'px'
				}).show();
			}
		}).mouseout(function(){
			//$(this).removeClass('open');
			if($(this).next('.sub-menu') != null)
			{
				var p = $(this).parent().position();
				$(this).next('.sub-menu').hide();
			}
		});
	});
	$('#menu-topmenu li .sub-menu').mouseover(function(){
		//$(this).prev('a').addClass('open');
		$(this).show();
	}).mouseout(function(){
		//$(this).prev('a').removeClass('open');
		$(this).hide();
	});

});