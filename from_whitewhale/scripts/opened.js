/* OpenEd Interaction Effects (requires jQuery) */
/* by White Whale Web Services */

$(function() { // on DOM ready
	var search = $('#search'),
		body = $('body');
	search.find('#search_query')
		.inlineLabel('Search') // then add inline search label
		.focus(function() { // when focusing
			search.addClass('open'); // open the panel
			body.click(function(e) { // if the body is clicked
				if(!$(e.target).parents('#search').length) { // if the clicked element is in the search box, return
					$('body').unbind('click'); // unbind the body click
					search.removeClass('open'); // and hide the search
				}
			});
		});
});

$.fn.extend({ // add jQuery plugins
	inlineLabel: function(text,style) {
		style = style || 'lw_inline_label'; // the default CSS class for placeholder text
		this.blur(function() { // onblur
			var val = $.trim($(this).val());
			if(!val||val==text) { // if this input has no contents or the contents are identical to the placeholder text
				$(this).addClass(style) // add the inline_label class
					.val(text) // set the appropriate text
					.one('focus',function() { // and, on the first focus
						$(this).val('') // remove that text
							.removeClass(style); // and the inline_label class
					});
			}
		}).blur(); // and do all this right now
		return this; // return original element for chaining
	}
});