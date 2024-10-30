<?php
/**
 * File advanced field class which users WordPress media popup to upload and select files.
 */
class CHWEBR_RWMB_File_Upload_Field extends CHWEBR_RWMB_File_Advanced_Field
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
		add_action( 'print_media_templates', array( __CLASS__, 'print_templates' ) );
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function admin_enqueue_scripts()
	{
		parent::admin_enqueue_scripts();
		wp_enqueue_style( 'chwebr-rwmb-upload', CHWEBR_RWMB_CSS_URL . 'upload.css', array( 'chwebr-rwmb-media' ), CHWEBR_RWMB_VER );
		wp_enqueue_script( 'chwebr-rwmb-file-upload', CHWEBR_RWMB_JS_URL . 'file-upload.js', array( 'chwebr-rwmb-media' ), CHWEBR_RWMB_VER, true );
	}

	/**
	 * Template for media item
	 * @return void
	 */
	static function print_templates()
	{
		require_once( CHWEBR_RWMB_INC_DIR . 'templates/upload.php' );
	}
}
