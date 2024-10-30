<?php
/**
 * Checkbox list field class.
 */
class CHWEBR_RWMB_Checkbox_List_Field extends CHWEBR_RWMB_Input_List_Field
{
	/**
	 * Normalize parameters for field
	 * @param array $field
	 * @return array
	 */
	static function normalize( $field )
	{
		$field['multiple'] = true;
		$field = parent::normalize( $field );		

		return $field;
	}
}
