<?php
/**
 * Validation module.
 * @package Meta Box
 */

/**
 * Validation class.
 */
class CHWEBR_RWMB_Validation
{
	/**
	 * Add hooks when module is loaded.
	 */
	public function __construct()
	{
		add_action( 'chwebr_rwmb_after', array( $this, 'rules' ) );
		add_action( 'chwebr_rwmb_enqueue_scripts', array( $this, 'scripts' ) );
	}

	/**
	 * Output validation rules of each meta box.
	 * The rules are outputted in [data-rules] attribute of an hidden <script> and will be converted into JSON by JS.
	 * @param CHWEBR_RW_Meta_Box $object Meta Box object
	 */
	public function rules( CHWEBR_RW_Meta_Box $object )
	{
		if ( ! empty( $object->meta_box['validation'] ) )
		{
			echo '<script type="text/html" class="chwebr-rwmb-validation-rules" data-rules="' . esc_attr( json_encode( $object->meta_box['validation'] ) ) . '"></script>';
		}
	}

	/**
	 * Enqueue scripts for validation.
	 */
	public function scripts()
	{
		wp_enqueue_script( 'jquery-validate', CHWEBR_RWMB_JS_URL . 'jquery.validate.min.js', array( 'jquery' ), CHWEBR_RWMB_VER, true );
		wp_enqueue_script( 'chwebr-rwmb-validate', CHWEBR_RWMB_JS_URL . 'validate.js', array( 'jquery-validate' ), CHWEBR_RWMB_VER, true );
		wp_localize_script( 'chwebr-rwmb-validate', 'rwmbValidate', array(
			'summaryMessage' => __( 'Please correct the errors highlighted below and try again.', 'meta-box' ),
		) );
	}
}
