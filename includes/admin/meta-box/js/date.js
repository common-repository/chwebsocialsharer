jQuery( function ( $ )
{
	'use strict';

	/**
	 * Update date picker element
	 * Used for static & dynamic added elements (when clone)
	 */
	function update()
	{
		var $this = $( this ),
			options = $this.data( 'options' ),
			$inline = $this.siblings( '.chwebr-rwmb-datetime-inline' ),
			$timestamp = $this.siblings( '.chwebr-rwmb-datetime-timestamp' ),
			current = $this.val();

		$this.siblings( '.ui-datepicker-append' ).remove(); // Remove appended text
		if ( $timestamp.length )
		{
			var $picker = $inline.length ? $inline : $this;
			options.onSelect = function ()
			{
				$timestamp.val( getTimestamp( $picker.datepicker( 'getDate' ) ) );
			};
		}

		if ( $inline.length )
		{
			options.altField = '#' + $this.attr( 'id' );
			$inline
				.removeClass( 'hasDatepicker' )
				.empty()
				.prop( 'id', '' )
				.datepicker( options )
				.datepicker( 'setDate', current );
		}
		else
		{
			$this.removeClass( 'hasDatepicker' ).datepicker( options );
		}
	}

	/**
	 * Convert date to Unix timestamp in milliseconds
	 * @link http://stackoverflow.com/a/14006555/556258
	 * @param date
	 * @return number
	 */
	function getTimestamp( date )
	{
		var milliseconds = Date.UTC( date.getFullYear(), date.getMonth(), date.getDate(), date.getHours(), date.getMinutes(), date.getSeconds() );
		return Math.floor( milliseconds / 1000 );
	}

	$( ':input.chwebr-rwmb-date' ).each( update );
	$( '.chwebr-rwmb-input' ).on( 'clone', ':input.chwebr-rwmb-date', update );
} );
