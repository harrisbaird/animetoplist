var dialogLoaded = false;

$(function() {
	hookLinks();

	$.tools.overlay.addEffect("drop",
	// loading animation
	function(done) {
		//Get the dialogs height
		var height = $('#dialog').show().height();
		var top = $(window).scrollTop() - height - 20;

		$('#dialog').css('top', top + 'px');

		var animateProps = {
			top: $(window).scrollTop() + 20
		};

		this.getOverlay().delay(300).animate(animateProps, "400", 'easeOutCubic', done).show();
	},

	// closing animation
	function(done) {
		var height = $('#dialog').show().height();
		var top = $(window).scrollTop() - height;

		var animateProps = {
			top: top
		};
		this.getOverlay().animate(animateProps, "fast", 'easeInCubic', function() {
			$(this).hide();
			done.call();
		});
	}
);
});

function switchTab(id) {
	$('#dialog-register, #dialog-login, #dialog-forgotten').hide();
	$('#' + id).show();
}

function hookLinks() {
	//Show login dialog
	$('#login-link, .dialog-switch.login').click(function(e) {
		e.preventDefault();
		loadDialog('dialog-login');
	});

	//Show register dialog
	$('#register-link, .dialog-switch.register, .register-dialog').click(function(e) {
		e.preventDefault();
		loadDialog('dialog-register');
	});

	//Login required links
	$('.login-required').click(function(e) {
		e.preventDefault();

		var message = null;
		var title = $(this).attr("title");
		if(title) {
			message = title;
		}

		loadDialog('dialog-login', message);
	});

	//Show forgotten password dialog
	$('.dialog-switch.forgotten').click(function(e) {
		e.preventDefault();
		loadDialog('dialog-forgotten');
	});

	$('#dialog-login #UserDialogForm').attr('action', '/users/login?next=' + window.location.href);

	$('#logout-link').click(function(e) {
		$.get('/users/logout');
	});
}

function fixCufon() {
	//Cufon.replace('h2, h3');
}

function loadDialog(tab, message, loaded) {
	if(!dialogLoaded) {
		$.get('/users/dialog', function(data) {
			$('body').prepend(data);
			hookLinks();
			displayDialog(tab, message);
			//fixCufon();
			//fixChromeBug();
			dialogLoaded = true;
			if(loaded) loaded();
		});
	} else {
		displayDialog(tab, message);
		if(loaded) loaded();
	}
}

function displayDialog(tab, message) {
	switchTab(tab);

	if(message) {
		$('#dialog .dialog-message h2 strong').val(message);
		$('#dialog .dialog-message h2').show();
	} else {
		$('#dialog .dialog-message h2').hide();
	}

	$('#dialog').overlay({
		effect: 'drop',
		expose: {
			color: '#000',
			loadSpeed: 300,
			opacity: 0.3
		},
		api: true
	}).load();
}
