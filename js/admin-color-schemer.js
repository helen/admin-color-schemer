(function($){
	$('.colorpicker').wpColorPicker();

	$('.show-advanced a').on('click', function(e){
		e.preventDefault();
		var $this = $(this);

		$this.parent().prev('.schemer-advanced').show();
		$this.remove();
	});

	$('#preview').on('click', function(e){
		e.preventDefault();

		// clear any existing messages
		$('h2').next('div').remove();

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: $('.color-schemer-pickers').serialize(),
			success: function(r) {
				if ( typeof r.errors != 'undefined' ) {
					$('h2').after( '<div class="error"><p>' + r.message + '</p></div>' );
				} else if ( typeof r.uri != 'undefined' ) {
					// Default admin color scheme doesn't have a #colors-css link to hot load into so let's make one if we need to
					if( ! $('#colors-css').length ) {
						$('head').append("<link rel='stylesheet' id='colors-css' href='' media='all' />");
					}

					$('#colors-css').attr('href', r.uri);
					$('h2').after( '<div class="update-nag">' + r.message + '</div>' );
				}
			}
		})
	})
})(jQuery);
