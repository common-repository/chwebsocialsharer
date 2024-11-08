<?php
/**
 * Password field class.
 */
class CHWEBR_RWMB_Password_Field extends CHWEBR_RWMB_Text_Field
{
	/**
	 * Get the attributes for a field
	 *
	 * @param array $field
	 * @param mixed $value
	 * @return array
	 */
	static function get_attributes( $field, $value = null )
	{
		$attributes         = parent::get_attributes( $field, $value );
		$attributes['type'] = 'password';
		return $attributes;
	}

	/**
	 * Store secured password in the database.
	 * @param mixed $new
	 * @param mixed $old
	 * @param int   $post_id
	 * @param array $field
	 * @return string
	 */
	static function value( $new, $old, $post_id, $field )
	{
		$new = $new != $old ? wp_hash_password( $new ) : $new;
		return $new;
	}
}
