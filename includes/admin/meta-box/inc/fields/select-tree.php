<?php
/**
 * Select tree field class.
 */
class CHWEBR_RWMB_Select_Tree_Field extends CHWEBR_RWMB_Select_Field
{
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
		$walker = new CHWEBR_RWMB_Select_Tree_Walker( $db_fields, $field, $meta );
		return $walker->walk( $options );
	}

	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts()
	{
		parent::admin_enqueue_scripts();
		wp_enqueue_style( 'chwebr-rwmb-select-tree', CHWEBR_RWMB_CSS_URL . 'select-tree.css', array( 'chwebr-rwmb-select' ), CHWEBR_RWMB_VER );
		wp_enqueue_script( 'chwebr-rwmb-select-tree', CHWEBR_RWMB_JS_URL . 'select-tree.js', array( 'chwebr-rwmb-select' ), CHWEBR_RWMB_VER, true );
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	public static function normalize( $field )
	{
		$field['multiple'] = true;
		$field['size']     = 0;
		$field             = parent::normalize( $field );

		return $field;
	}

	/**
	 * Get the attributes for a field
	 *
	 * @param array $field
	 * @param mixed $value
	 *
	 * @return array
	 */
	public static function get_attributes( $field, $value = null )
	{
		$attributes             = parent::get_attributes( $field, $value );
		$attributes['multiple'] = false;
		$attributes['id']       = false;

		return $attributes;
	}
}
