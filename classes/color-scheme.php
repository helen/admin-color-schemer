<?php
defined( 'WPINC' ) or die;

class Admin_Color_Schemer_Scheme {
	protected $base;
	protected $highlight;
	protected $notification;
	protected $button;
	protected $accessors = array( 'base', 'highlight', 'notification', 'button' );

	public function __construct( $colors = NULL ) {
		if ( is_array( $colors ) ) {
			foreach ( $this->accessors as $thing ) {
				if ( isset( $colors[$thing] ) ) {
					$this->{$thing} = $colors[$thing];
				}
			}
		}
	}

	public function __get( $key ) {
		if ( in_array( $key, $this->accessors ) ) {
			return $this->{$key};
		}
	}

	public function __set( $key, $value ) {
		if ( in_array( $key, $this->accessors ) ) {
			$this->{$key} = $value;
		}
	}
}
