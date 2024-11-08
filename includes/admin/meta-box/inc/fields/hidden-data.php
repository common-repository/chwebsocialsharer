<?php

/**
 * Custom HTML field class.
 */
class CHWEBR_RWMB_Hidden_Data_Field extends CHWEBR_RWMB_Field {

		/**
		 * Get field HTML
		 *
		 * @param mixed $meta
		 * @param array $field
		 *
		 * @return string
		 */
		static function html( $meta, $field )
		{
			return sprintf(
				'<input type="hidden" class="rwmb-hidden" name="%s" id="%s" value="%s">',
				$field['field_name'],
				$field['id'],
				$field['std']
			);
		}



}
