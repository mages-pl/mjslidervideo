{* url: {$slider_video} *}
{if Configuration::get('enable_slider_video') == '1'}
<div class="home_mjslidervideo">
<video autoplay="autoplay" loop="loop" muted defaultMuted playsinline id="myVideo">
  <source src="{$slider_video}" type="video/mp4">
</video>
</div>
{/if}