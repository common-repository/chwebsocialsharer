window.rwmb = window.rwmb || {};

jQuery( function ( $ )
{
	'use strict';

	var views = rwmb.views = rwmb.views || {},
		ImageField = views.ImageField,
		ImageUploadField,
		UploadButton = views.UploadButton;

	ImageUploadField = views.ImageUploadField = ImageField.extend( {
		createAddButton: function ()
		{
			this.addButton = new UploadButton( { collection: this.collection, props: this.props } );
		}
	} );

	/**
	 * Initialize fields
	 * @return void
	 */
	function init()
	{
		new ImageUploadField( { input: this, el: $( this ).siblings( 'div.chwebr-rwmb-media-view' ) } );
		console.log('win');
	}
	$( ':input.chwebr-rwmb-image_upload, :input.chwebr-rwmb-plupload_image' ).each( init );
	$( '.chwebr-rwmb-input' )
		.on( 'clone', ':input.chwebr-rwmb-image_upload, :input.chwebr-rwmb-plupload_image', init )
} );
