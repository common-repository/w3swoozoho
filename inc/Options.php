<?php

namespace W3SCloud\WooZoho;

/**
 * Class Options.
 *
 * @package W3SCloud\WooZoho
 */
class Options {
	/**
	 * Get the value of a settings field
	 *
	 * @param string      $option settings field name.
	 * @param string|null $section the section name this field belongs to.
	 * @param boolean     $default default text if it's not found.
	 *
	 * @return mixed
	 */
	public static function get_option( $option, $section, $default = false ) {
		if ( ! isset( $section ) ) {
			return get_option( $option );
		}

		$options = get_option( $section );

		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}

		return $default;
	}
}
