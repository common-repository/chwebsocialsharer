<?php
/**
 * Select advanced field which uses select2 library.
 */
class CHWEBR_RWMB_Select_Advanced_Field extends CHWEBR_RWMB_Select_Field
{
	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts()
	{
		parent::admin_enqueue_scripts();
		wp_enqueue_style( 'chwebr-rwmb-select2', CHWEBR_RWMB_CSS_URL . 'select2/select2.css', array(), '4.0.1' );
		wp_enqueue_style( 'chwebr-rwmb-select-advanced', CHWEBR_RWMB_CSS_URL . 'select-advanced.css', array(), CHWEBR_RWMB_VER );

		wp_register_script( 'chwebr-rwmb-select2', CHWEBR_RWMB_JS_URL . 'select2/select2.min.js', array(), '4.0.2', true );

		//Localize
		$deps  = array( 'chwebr-rwmb-select2', 'chwebr-rwmb-select' );
		$dir   = CHWEBR_RWMB_JS_URL . 'select2/i18n/';
		$file  = str_replace( '_', '-', get_locale() );
		$parts = explode( '-', $file );
		$file  = file_exists( CHWEBR_RWMB_DIR . 'js/select2/i18n/' . $file . '.js' ) ? $file : $parts[0];

		if ( file_exists( CHWEBR_RWMB_DIR . 'js/select2/i18n/' . $file . '.js' ) )
		{
			wp_register_script( 'chwebr-rwmb-select2-i18n', $dir . $file . '.js', array( 'chwebr-rwmb-select2' ), '4.0.2', true );
			$deps[] = 'chwebr-rwmb-select2-i18n';
		}

		wp_enqueue_script( 'chwebr-rwmb-select', CHWEBR_RWMB_JS_URL . 'select.js', array(), CHWEBR_RWMB_VER, true );
		wp_enqueue_script( 'chwebr-rwmb-select-advanced', CHWEBR_RWMB_JS_URL . 'select-advanced.js', $deps, CHWEBR_RWMB_VER, true );

	}

	/**
	 * Walk options
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @param mixed $options
	 * @param mixed $db_fields
	 *
	 * @return string
	 */
	public static function walk( $options, $db_fields, $meta, $field )
	{
		$attributes = call_user_func( array( CHWEBR_RW_Meta_Box::get_class_name( $field ), 'get_attributes' ), $field, $meta );
		$walker     = new CHWEBR_RWMB_Select_Walker( $db_fields, $field, $meta );
		$output     = sprintf(
			'<select %s>',
			self::render_attributes( $attributes )
		);

		$output .= '<option></option>';
		$output .= $walker->walk( $options, $field['flatten'] ? - 1 : 0 );
		$output .= '</select>';
		$output .= self::get_select_all_html( $field );
		return $output;
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	public static function normalize( $field )
	{
		$field = wp_parse_args( $field, array(
			'js_options'  => array(),
			'placeholder' => 'Select an item',
		) );

		$field = parent::normalize( $field );

		$field['js_options'] = wp_parse_args( $field['js_options'], array(
			'allowClear'  => true,
			'width'       => 'none',
			'placeholder' => $field['placeholder'],
		) );

		return $field;
	}

	/**
	 * Get the attributes for a field
	 *
	 * @param array $field
	 * @param mixed $value
	 * @return array
	 */
	public static function get_attributes( $field, $value = null )
	{
		$attributes = parent::get_attributes( $field, $value );
		$attributes = wp_parse_args( $attributes, array(
			'data-options' => wp_json_encode( $field['js_options'] ),
		) );

		return $attributes;
	}
}
