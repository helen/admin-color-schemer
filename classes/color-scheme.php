<?php
defined( 'WPINC' ) or die;

class Admin_Color_Schemer_Scheme {
	// possibly-temporary default
	protected $id = 1;
	protected $slug;
	protected $name;
	protected $uri;
	protected $accessors = array( 'id', 'slug', 'name', 'uri', 'icon_focus', 'icon_current' );

	// Icon colors for SVG painter - likely temporary placement, as it will need some more special handling
	protected $icon_color = '#fff';
	protected $icon_focus = '#fff';
	protected $icon_current = '#fff';

	public function __construct( $attr = NULL ) {
		// extend accessors
		$admin_schemer = Admin_Color_Schemer_Plugin::get_instance();
		$this->accessors = array_merge( $this->accessors, array_keys( $admin_schemer->get_colors() ) );

		// set slug
		$this->slug = 'admin_color_schemer_' . $this->id;

		if ( is_array( $attr ) ) {
			foreach ( $this->accessors as $thing ) {
				if ( isset( $attr[$thing] ) && ! empty( $attr[$thing] ) ) {
					$this->{$thing} = $attr[$thing];
				}
			}
		} else {
			// set defaults
			// @todo: make this really set defaults for the items that must have a color - what are those?
			$this->name = __( 'Custom', 'admin-color-schemer' );
		}
	}

	public function __get( $key ) {
		if ( in_array( $key, $this->accessors ) ) {
			if ( isset( $this->{$key} ) ) {
				return $this->sanitize( $this->{$key}, $key, 'out' );
			} else {
				return false;
			}
		}
	}

	public function __set( $key, $value ) {
		if ( in_array( $key, $this->accessors ) ) {
			$this->{$key} = $this->sanitize( $value, $key, 'in' );
		}
	}

	public function __isset( $key ) {
		return isset( $this->$key );
	}

	private function sanitize( $value, $key, $direction ) {
		switch ( $key ) {
			case 'id':
				$value = absint( $value );
				break;
			case 'slug':
				$value = sanitize_key( $value );
			case 'name':
				$value = esc_html( $value );
				break;
			case 'uri':
				$value = esc_url_raw( $value );
				break;
			default:
				// everything else should be a hex value
				// regex copied from core's sanitize_hex_value()
				if ( ! preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $value ) ) {
					$value = '';
				}
				break;
		}
		return $value;
	}

	public function to_array() {
		$return = array();
		foreach ( $this->accessors as $thing ) {
			$return[$thing] = $this->{$thing};
		}
		return $return;
	}
}
