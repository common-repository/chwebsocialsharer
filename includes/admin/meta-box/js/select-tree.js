jQuery( function( $ )
{
	'use strict';

	function update()
	{
		var $this = $( this ),
			val = $this.val(),
			$selected = $this.siblings( "[data-parent-id='" + val + "']" ),
			$notSelected = $this.parent().find( '.chwebr-rwmb-select-tree' ).not( $selected );

		$selected.removeClass( 'hidden' );
		$notSelected
			.addClass( 'hidden' )
			.find( 'select' )
			.prop( 'selectedIndex', 0 );
	}

	$( '.chwebr-rwmb-input' )
		.on( 'change', '.chwebr-rwmb-select-tree select', update )
		.on( 'clone', '.chwebr-rwmb-select-tree select', update );
} );
