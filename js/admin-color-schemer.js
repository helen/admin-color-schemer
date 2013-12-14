(function($){
	$('.colorpicker').wpColorPicker();

	$('.show-advanced a').on('click', function(e){
		e.preventDefault();
		var $this = $(this);

		$this.parent().prev('.schemer-advanced').show();
		$this.remove();
	});
})(jQuery);
