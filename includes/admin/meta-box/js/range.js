jQuery( function ( $ )
{
	'use strict';

	/**
	 * Update color picker element
	 * Used for static & dynamic added elements (when clone)
	 */
	function update()
	{
		var $this = $( this ),
			$output = $this.siblings( '.chwebr-rwmb-output' );

    $this.on( 'input propertychange change', function( e )
    {
      $output.html( $this.val() );
    } );

	}

	$( ':input.chwebr-rwmb-range' ).each( update );
	$( '.chwebr-rwmb-input' ).on( 'clone', 'input.chwebr-rwmb-range', update );
} );
