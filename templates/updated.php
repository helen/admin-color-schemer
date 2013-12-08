<?php
defined( 'WPINC' ) or die;
?>
<div class="updated">
	<p><?php _e( 'Settings updated!', 'admin-color-schemer' ); ?></p>
</div>
<script>
(function(){
	if ( window.history && window.history.pushState ) {
		window.history.replaceState( {}, '', window.location.toString().replace( /&updated=true/, '' ) );
	}
})();
</script>
