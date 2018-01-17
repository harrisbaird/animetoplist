(function ($) {
$.fn.vAlign = function() {
	return this.each(function(i){
	var h = $(this).height();
	var oh = $(this).outerHeight();
	var mt = (h + (oh - h)) / 2;
	$(this).css("margin-top", "-" + mt + "px");
	$(this).css("top", "50%");
	$(this).css("position", "absolute");
	$(this).parent().css("position", "relative");
	});
};
})(jQuery);


(function($){
  $.fn.shuffle = function() {
    return this.each(function(){
      var items = $(this).children();
      return (items.length)
        ? $(this).html($.shuffle(items))
        : this;
    });
  }

  $.shuffle = function(arr) {
    for(
      var j, x, i = arr.length; i;
      j = parseInt(Math.random() * i),
      x = arr[--i], arr[i] = arr[j], arr[j] = x
    );
    return arr;
  }
})(jQuery);

//if(top != self) top.location.href = self.location.href;

$(function() {
	$(".premium-text ul.random").shuffle();
	$('#sitesTable tbody tr.boost').prependTo('#sitesTable tbody');

	$(".carousel ul").shuffle();
	$(".carousel").show();
	$(".carousel").carousel({
		loop: true,
		autoSlide: true,
		autoSlideInterval: 5000
	});

//	$("img[class!=no-lazyload]").lazyload({
//	    effect : "fadeIn"
//	});

	$('.toggle').click(function() {
		$(this).parent().parent().find('.contents').slideToggle('fast');
		$(this).parent().parent().toggleClass("open");
	});

	//Center elements
	$('.jcenter').each(function(i) {

		//var centerPWidth = $(this).parent().width();
		//var centerEWidth = $(this).width();
		//var marginL = (centerPWidth / 2) - (centerEWidth / 2);
		//$(this).parent().css('padding-left', marginL);
		//$(this).css('margin-left', 0);
	});

	//Featured series hover
	$('.featured-series').hover(function() {
		$(this).find('.overlay').animate({bottom: '0px'}, 150);
		$(this).find('.overlay-bottom').fadeIn(150);
	}, function() {
		$(this).find('.overlay').animate({bottom: '-80px'}, 150);
		$(this).find('.overlay-bottom').fadeOut(150);
	});

	//Delete link
	$('.delete').click(function() {
		var siteName = $(this).attr('rel');
		return confirm('Are you sure you want to delete ' + siteName + '?');
	});

	//Vertical align for MSIE7
	if($.browser.msie && $.browser.version == "7.0") {
		$(".module.icons li a span").vAlign();
	}

	//External links in new window
	$('.external').attr('target','_blank');
});

//Google Analytics
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-250587-2']);
_gaq.push(['_trackPageview']);

(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();

//Handle comments
Comment = {
	reply: function(parentId, origin) {
		var divId = 'comment-reply-' + parentId;
		var divClass = '#' + divId;
		var location = $(origin).closest('li').find('p');
		var user = $(origin).closest('div.comment-header').find('span.author').html();

//		console.log(origin);

		if($(divClass).length > 0) {
			//Div exists, toggle it
			$(divClass).toggle();
			 $(divClass + ' #CommentBody').focus();
		} else {
			//Clone the form
			$('<div>').attr({id: divId}).addClass('comment-reply').insertAfter(location);
			$('#commentForm').clone().appendTo(divClass);

			//Update the parent id
			$(divClass).find('#CommentParentId').val(parentId);

			//Who is the user replying to


			//Update the text
			$(divClass).find('button').html('<span>Post Reply</span>');
			$(divClass).find('label').html('Reply to ' + user);

		 	$(divClass + ' #CommentBody').focus();
		}

		return false;
	}
}
