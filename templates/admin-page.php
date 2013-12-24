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
			$admin_schemer = Admin_Color_Schemer_Plugin::get_instance();
			$loops = $admin_schemer->get_colors( 'basic' );
			foreach ( $loops as $handle => $nicename ): ?>

			<tr valign="top">
				<th scope="row"><label for="color-<?php echo $handle; ?>"><?php echo esc_html( $nicename ); ?></label></th>
				<td><input name="<?php echo $handle; ?>" type="text" id="color-<?php echo $handle; ?>" value="<?php echo esc_attr( $scheme->{$handle} ); ?>" class="colorpicker" autocomplete="off" /></td>
			</tr>

			<?php endforeach; ?>

		</table>

		<table class="form-table schemer-advanced hide-if-js">
			<?php
			$admin_schemer = Admin_Color_Schemer_Plugin::get_instance();
			$loops = $admin_schemer->get_colors( 'advanced' );
			foreach ( $loops as $handle => $nicename ): ?>

			<tr valign="top">
				<th scope="row"><label for="color-<?php echo $handle; ?>"><?php echo esc_html( $nicename ); ?></label></th>
				<td><input name="<?php echo $handle; ?>" type="text" id="color-<?php echo $handle; ?>" value="<?php echo esc_attr( $scheme->{$handle} ); ?>" class="colorpicker" autocomplete="off" /></td>
			</tr>

			<?php endforeach; ?>

		</table>

		<p class="show-advanced"><a href="#" class="hide-if-no-js">Show advanced options</a></p>

		<p>
			<?php submit_button( __('Save and Use', 'admin-color-schemer' ), 'primary', 'submit', false ); ?>
			<?php submit_button( __( 'Preview', 'admin-color-schemer' ), 'secondary preview-scheme hide-if-no-js', 'preview', false ); ?>
		</p>
	</form>
</div>