<?php
/**
 * File advanced field class which users WordPress media popup to upload and select files.
 */
class CHWEBR_RWMB_Image_Upload_Field extends CHWEBR_RWMB_Image_Advanced_Field
{
	/**
	 * Add actions
	 *
	 * @return void
	 */
	static function add_actions()
	{
		parent::add_actions();
		// Print attachment templates
		add_action( 'print_media_templates', array( 'CHWEBR_RWMB_File_Upload_Field', 'print_templates' ) );
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function admin_enqueue_scripts()
	{
		parent::admin_enqueue_scripts();
		CHWEBR_RWMB_File_Upload_Field::admin_enqueue_scripts();
		wp_enqueue_script( 'chwebr-rwmb-image-upload', CHWEBR_RWMB_JS_URL . 'image-upload.js', array( 'chwebr-rwmb-file-upload', 'chwebr-rwmb-image-advanced' ), CHWEBR_RWMB_VER, true );
	}
}
