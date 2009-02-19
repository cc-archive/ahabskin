<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Open Ed at Creative Commons</title>
	<style type="text/css">
		/* Reset browser defaults  */
		html { font-size:100%; }
		body,p,h1,h2,h3,h4,h5,h6,ul,ol,li,dl,dt,dd,table,th,td,pre,img,form,fieldset,legend,label,iframe { margin:0; padding:0; font-size:1em; line-height:inherit; font-weight:inherit; border:none; }
		input,select,textarea,button { font-size:1em; line-height:inherit; font-family:inherit; color:#222; margin:0; }
		input[type=button],button { overflow:visible; }
		input[disabled],select[disabled],textarea[disabled],button[disabled] { opacity:0.75; }
		ul { list-style-type:none; }
		.skiplink { position:absolute; top:0; left:-999px; width:9em; padding:5px; color:#00f; background-color:#ff9; border:1px solid #993; text-align:center; z-index:9999; }
		.skiplink:focus { left:0; }
		.hidden { display:none; }
	
		/* Page structure */
		body { padding:1em 0 0; font-family:sans-serif; font-size:0.875em; line-height:1.5em; }
		#container { overflow:hidden; position:relative; }
		#controls { position:absolute; top:80px; right:150px; background-color:#555; color:#fff; width:15em; padding:1em; font-weight:bold;  display:none; }
			label { margin:0 0.5em; }
			#container input[type=text] { width:3em; }
		#top { width:1046px; height:373px; margin:0 auto; background-image:url(/images/cc-top.gif); }
		#strip  { width:1046px; height:360px; margin:0 auto; overflow:visible; position:relative; }
			#scroll { width:40px; position:absolute; top:-25px; right:5px; }
				#scroll_left,#scroll_right { background-image:url(/images/cc-arrows.gif); height:20px; width:20px; float:left; }
				#scroll_left { background-position:0 0; }
					#scroll_left:hover { background-position:0 -20px; }
					#scroll_left.active { background-position:0 -40px; }
				#scroll_right { background-position:-20px 0; }
					#scroll_right:hover { background-position:-20px -20px; }
					#scroll_right.active { background-position:-20px -40px; }
			#blocker_left,#blocker_right { width:1000px; height:360px; position:absolute; background-color:rgba(255,255,255,0.7); border:0px solid #c8c8c8; z-index:200; }
			#blocker_left { left:-1000px; border-right-width:1px; }
			#blocker_right { right:-1000px; border-left-width:1px; }
			#slider { width:2094px; height:360px; position:absolute; left:-524px; background-image:url(/images/cc-slider.jpg); background-position:-1px 0; }
		#bottom { width:1046px; height:148px; margin:0 auto; background-image:url(/images/cc-bottom.gif); }
	</style>
</head>
<body>
	<a href="#content" class="skiplink">Skip to content</a>
		<div id="container">
		<div id="controls">
			<ul>
				<li><input type="checkbox" id="onepanel" checked="checked"/><label for="onepanel">Scroll one panel at a time</label></li>
				<li><input type="checkbox" id="holdbutton"/><label for="holdbutton">Hold button to scroll</label></li>
				<li><input type="checkbox" id="loop"/><label for="loop">Infinite loop</label></li>
				<li><input type="checkbox" id="endbounce" checked="checked"/><label for="endbounce">Bounce at ends</label></li>
				<li><label for="easing">Easing</label><select id="easing"></select></li>
				<li><label for="speed">Speed</label><input type="text" id="speed" value="600"/></li>
			</ul>
		</div>
		<div id="top"></div>
		<div id="strip">
			<div id="scroll"><div id="scroll_left"></div><div id="scroll_right"></div></div>
			<div id="blocker_left"></div>
			<div id="blocker_right"></div>
			<div id="slider"></div>
		</div>
		<div id="bottom"></div>
	</div>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.3.js"></script>
<script type="text/javascript" src="/scripts/jquery-ui-effects.core-1.6rc6.min.js"></script>
<script type="text/javascript">
$(function() { // on DOM ready
	var s = {
		onepanel : true,
		holdbutton : true,
		endbounce : true,
		easing : 'easeInOutSine',
		speed : 600,
		loop:false
	};
	
	var slider = $('#slider'),
		clicktimer,
		bouncing,
		position = parseInt(slider.css('left')),
		mousedown = function(direction) {
			var moving = (direction=='left' ? -1 : 1);
			if(!s.loop&&((direction=='right'&&position>-262) || (direction=='left'&&position<-1046))) {
				if(s.endbounce) {
					if(!bouncing) {
						bouncing = true;
						slider.animate({left:'+='+(moving*40)+'px'},250,'easeInQuad',function() {
							slider.animate({left:'+='+(-1*moving*40)+'px'},500,'easeOutBack',function() {
								bouncing = false;
							});
						});
					}
				}
				return;
			} else {
				position+=moving*262;
				slider.animate({left:position+'px'},s.speed,(s.onepanel ? s.easing : 'linear'));
				if(s.holdbutton) clicktimer = setTimeout(function() { mousedown(direction); },s.speed);
			}
		},
		mouseup = function() {
			if(s.holdbutton) clearTimeout(clicktimer);
			if(!s.onepanel) slider.stop();
		};
		
	$('#scroll_left,#scroll_right').mousedown(function() {
		$(this).addClass('active');
		mousedown($(this).attr('id').replace('scroll_',''));
	}).mouseup(function() {
		$(this).removeClass('active');
		mouseup();	
	});
	
	$('#top').click(function() {
		$('#controls').toggle();
	});
	$(':input').removeAttr('disabled');
	$('#onepanel').attr('checked','checked').click(function() {
		s.onepanel = $(this).attr('checked') ? true : false;
		if(s.onepanel) {
			slider.css('left',(position-(position%262))+'px')
			$('#holdbutton').removeAttr('disabled');
		} else {
			$('#holdbutton').attr('checked','checked').attr('disabled','disabled').triggerHandler('click');
			$('#endbounce').removeAttr('checked').attr('disabled','disabled').triggerHandler('click');
		}
	});
	$('#holdbutton').attr('checked','checked').click(function() {
		s.holdbutton = $(this).attr('checked') ? true : false;
	});
	$('#endbounce').attr('checked','checked').click(function() {
		s.endbounce = $(this).attr('checked') ? true : false;
	});
	$('#loop').removeAttr('checked').click(function() {
		s.loop = $(this).attr('checked') ? true : false;
		if(s.loop) {
			position = -52400;
			slider.css({width:'209400px',left:position});
			$('#endbounce').removeAttr('checked').attr('disabled','disabled').triggerHandler('click');
		} else {
			position = -524;
			slider.css({width:'2094px',left:position});
			$('#endbounce').removeAttr('disabled');
		}
	});
	$('#speed').val('600').keyup(function() {
		s.speed = parseInt($(this).val());
	});
	$.each(jQuery.easing,function(key) {
		$('#easing').append('<option'+(key==s.easing ? ' selected="selected"' : '')+'>'+key+'</option>');
	});
	$('#easing').change(function() {
		s.easing = $(this).val();
	});
});
</script>
</body>
</html>