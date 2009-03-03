/* OpenEd Homepage Effects (requires jQuery) */
/* by White Whale Web Services */

$(function() { // on DOM ready
	// Nav hover
	var nav = $('#p-navigation');
	nav.find('li').hover(function() {
		nav.css('background-position','0 '+($(this).position().top-30)+'px')
	}, function() {
		nav.css('background-position','0 50px')
	});
	
	// Feature panel
	var panelWidth = 242, // panel width, including righthand margin
		pageWidth = panelWidth*4, // 4 panels showing at a time
		slider = $('#slider'),
		sliderWidth = slider.children().length*panelWidth, // the number of panels * the width
		bouncing = false, // we're not currently animating a bounce
		position = parseInt(slider.css('left')); // get current position		
	slider.width(sliderWidth);
	slider.find('>li:odd').addClass('alternate'); // add classes for alternating strip styles
	$('#content').append('<div id="mask_left"/><div id="mask_right"/><div id="scroll_navigation"><div id="scroll_left"/><div id="scroll_right"/></div>') // add elements specific to JS interaction
		.css('overflow','visible'); // and make sure that's all visible
	$('#scroll_left,#scroll_right').mousedown(function() {
		$(this).addClass('active');
		var direction = $(this).attr('id').replace('scroll_',''),
			moving = (direction=='left' ? 1 : -1);
		if((direction=='left'&&position>-1*panelWidth) || (direction=='right'&&position<-1*sliderWidth+pageWidth+panelWidth)) { // if we've reached the end of the strip
			if(!bouncing) { // and we're not currently animating a bounce
				bouncing = true; // flag that we're animating a bounce
				slider.animate({left:'+='+(moving*40)+'px'},250,'easeInQuad',function() { // and do it
					slider.animate({left:'+='+(-1*moving*40)+'px'},500,'easeOutBack',function() {
						bouncing = false; // kill the flag now that we're done
					});
				});
			}
		} else {
			position+=moving*panelWidth; // otherwise, we just need to move the strip
			slider.animate({left:position+'px'},500,'easeInOutSine');
		}
	}).mouseup(function() {
		$(this).removeClass('active');
	});
});
