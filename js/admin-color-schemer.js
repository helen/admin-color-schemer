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
					$('#colors-css').attr('href', r.uri);
					$('h2').after( '<div class="update-nag">' + r.message + '</div>' );
				}
			}
		})
	})
})(jQuery);
