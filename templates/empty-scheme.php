<?php
defined( 'WPINC' ) or die;
?>
<div class="error">
	<p><?php _e( 'Please make more selections to save the color scheme.', 'admin-color-schemer' ); ?></p>
</div>
<script>
(function(){
	if ( window.history && window.history.pushState ) {
		window.history.replaceState( {}, '', window.location.toString().replace( /&empty_scheme=true/, '' ) );
	}
})();
</script>
