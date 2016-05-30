{*
 Extra fields for DataBC
*}

<div id="DataBC_help">
  <div class="help">
    {ts}Please note: if you have selected DataBC above - you will likely want to select a backup method - to GeoCode non-BC addresses - on the {/ts}
    <a href="databcgeocode">DataBC admin settings page</a>
  </div>
</div>

{literal}<script type="text/javascript">
  cj(function ($) {
    ($('#DataBC_help')).insertAfter($('.crm-map-form-block-geoAPIKey'));
  });
</script>
{/literal}
