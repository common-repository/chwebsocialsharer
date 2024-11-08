jQuery( function( $ )
{
  function update()
  {
    var $this = $( this ),
      $children = $this.closest( 'li' ).children('ul');

    if ( $this.is( ':checked' ) )
    {
      $children.removeClass( 'hidden' );
    }
    else
    {
      $children
        .addClass( 'hidden' )
        .find( 'input' )
        .removeAttr( 'checked' );
    }
  }

  $( '.chwebr-rwmb-input' )
    .on( 'change', '.chwebr-rwmb-input-list.collapse :checkbox', update )
    .on( 'clone', '.chwebr-rwmb-input-list.collapse :checkbox', update );
  $( '.chwebr-rwmb-input-list.collapse :checkbox' ).each( update );
} );
