<?php
defined( 'WPINC' ) or die;
?>
<div class="wrap">
	<h2><?php echo esc_html( $GLOBALS['title'] ); ?></h2>

	<form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" class="color-schemer-pickers postbox">
		<input type="hidden" name="action" value="admin-color-schemer-save" />
		<?php wp_nonce_field( self::NONCE ); ?>
		<table class="form-table">
			<?php
			$loops = array(
				'base' => __( 'Base', 'admin-color-schemer' ),
				'icon' => __( 'Icon', 'admin-color-schemer' ),
				'highlight' => __( 'Highlight', 'admin-color-schemer' ),
				'notification' => __( 'Notification', 'admin-color-schemer' ),
			);
			foreach ( $loops as $handle => $nicename ): ?>

			<tr valign="top">
				<th scope="row"><label for="color-<?php echo $handle; ?>"><?php echo esc_html( $nicename ); ?></label></th>
				<td><input name="<?php echo $handle; ?>" type="text" id="color-<?php echo $handle; ?>" value="<?php echo esc_attr( $scheme->{$handle} ); ?>" class="colorpicker" /></td>
			</tr>

			<?php endforeach; ?>

		</table>
		<?php submit_button(); ?>
	</form>
</div>