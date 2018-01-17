$(function() {
	$('#search input').attr('autocomplete', 'off');

	//Search box
	$('#search input').focus(function() {
		if($(this).val() == 'Search') {
			$(this).val('');
		}
	});

	$('#search input').blur(function() {
		if($(this).val() == '') {
			$(this).val('Search');
		}
		$('#autocomplete').fadeOut('fast');
	});

	$('#search input').bind('keyup', function(e) {
		//Ignore searches less than 3 characters
		if($(this).val().length < 3) return false;
		if(e.which < 48 || e.which > 122) return false;

		var url = '/search/autocomplete/' + urlencode($(this).val());

		//Fetch the JSON data
		var xhr = $.ajax({
			url: url,
			dataType: 'json',
			beforeSend: function() {
				$('#search button').addClass('loading');
			},
			success: function(data) {
				$('#search button').removeClass('loading');

				if($.isEmptyObject(data) == true) {
					$('#autocomplete').fadeOut('fast');
					return false;
				}

				$('#autocomplete').html('');				
				$('#autocomplete').fadeIn('fast');

				$.each(data, function(i, category) {
					if(category.length == 0) return true;

					//Generate categories
					var ul = $('<ul />');
					$('<h4>'+i+'</h4>').appendTo('#autocomplete');

					//Generate links
					$.each(category, function(j, item) {
						var image = $('<div/>').attr({'class': 'auto-image'}).css('background', 'url('+item.image+')');

						var text = $('<a/>').attr({href: item.url}).html(item.html);

						var link = $('<a/>').html(' > ').attr({href: item.url}).addClass('auto-go');

						var div = $('<div/>').attr({'class': 'auto-text'}).append(text);



						$('<li/>').append(div).appendTo(ul);
						//$("<p/>").html(item.name).appendTo("#autocomplete");
					});

					$(ul).appendTo('#autocomplete');

				});
			}
		});

	});
});

function urlencode(str) {
	str = (str+'').toString();
	return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').
    	replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
}
