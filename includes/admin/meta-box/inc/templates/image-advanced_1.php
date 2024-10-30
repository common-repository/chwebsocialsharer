<script id="tmpl-chwebr-rwmb-image-item" type="text/html">
  <input type="hidden" name="{{{ data.fieldName }}}" value="{{{ data.id }}}" class="chwebr-rwmb-media-input">
  <div class="chwebr-rwmb-media-preview">
    <div class="chwebr-rwmb-media-content">
      <div class="centered">
        <# if ( 'image' === data.type && data.sizes ) { #>
          <# if ( data.sizes.thumbnail ) { #>
            <img src="{{{ data.sizes.thumbnail.url }}}">
          <# } else { #>
            <img src="{{{ data.sizes.full.url }}}">
          <# } #>
        <# } else { #>
          <# if ( data.image && data.image.src && data.image.src !== data.icon ) { #>
            <img src="{{ data.image.src }}" />
          <# } else { #>
            <img src="{{ data.icon }}" />
          <# } #>
        <# } #>
      </div>
    </div>
  </div>
  <div class="chwebr-rwmb-overlay"></div>
  <div class="chwebr-rwmb-media-bar">
    <a class="chwebr-rwmb-edit-media" title="{{{ i18nRwmbMedia.edit }}}" href="{{{ data.editLink }}}" target="_blank">
      <span class="dashicons dashicons-edit"></span>
    </a>
    <a href="#" class="chwebr-rwmb-remove-media" title="{{{ i18nRwmbMedia.remove }}}">
      <span class="dashicons dashicons-no-alt"></span>
    </a>
  </div>
</script>
