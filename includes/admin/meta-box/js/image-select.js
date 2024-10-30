jQuery( function ( $ )
{
	'use strict';

	$( 'body' ).on( 'change', '.chwebr-rwmb-image-select input', function ()
	{
		var $this = $( this ),
			type = $this.attr( 'type' ),
			selected = $this.is( ':checked' ),
			$parent = $this.parent(),
			$others = $parent.siblings();
		if ( selected )
		{
			$parent.addClass( 'chwebr-rwmb-active' );
			if ( type === 'radio' )
			{
				$others.removeClass( 'chwebr-rwmb-active' );
			}
		}
		else
		{
			$parent.removeClass( 'chwebr-rwmb-active' );
		}
	} );
	$( '.chwebr-rwmb-image-select input' ).trigger( 'change' );
} );
