<?php
defined( 'WPINC' ) or die;

class Admin_Color_Schemer_Scheme {
	protected $id;
	protected $name;
	protected $accessors = array( 'id', 'name', 'uri', 'base', 'highlight', 'notification', 'icon', 'icon_focus', 'icon_current' );

	// Colors - might need some more defaults and a way of handling them on save
	protected $base;
	protected $highlight;
	protected $notification;
	protected $icon;
	protected $icon_focus = '#fff';
	protected $icon_current = '#fff';

	public function __construct( $attr = NULL ) {
		if ( is_array( $attr ) ) {
			foreach ( $this->accessors as $thing ) {
				if ( isset( $attr[$thing] ) ) {
					$this->{$thing} = $attr[$thing];
				}
			}
		}
	}

	public function __get( $key ) {
		if ( in_array( $key, $this->accessors ) ) {
			return $this->sanitize( $this->{$key}, $key, 'out' );
		}
	}

	public function __set( $key, $value ) {
		if ( in_array( $key, $this->accessors ) ) {
			$this->{$key} = $this->sanitize( $value, $key, 'in' );
		}
	}

	private function sanitize( $value, $key, $direction ) {
		switch ( $key ) {
			case 'id':
				$value = absint( $value );
				break;
			case 'base':
			case 'highlight':
			case 'notification':
			case 'icon':
			case 'icon_focus':
			case 'icon_current':
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
