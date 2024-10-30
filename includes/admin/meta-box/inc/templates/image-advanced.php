<script id="tmpl-chwebr-rwmb-image-item" type="text/html">
  <input type="hidden" name="{{{ data.fieldName }}}" value="{{{ data.id }}}" class="chwebr-rwmb-media-input">
  <!--<div class="chwebr-rwmb-media-preview" style="background-image: url({{{ data.sizes.full.url }}});">-->
  <img src="{{{ data.sizes.full.url }}}">
    <!--<div class="chwebr-rwmb-media-content">
      <div class="centered">
           <img src="{{{ data.sizes.full.url }}}">
      </div>
    </div>//-->
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
