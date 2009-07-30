{if is_set( $address_div_id )|not}
    {def $address_div_id=concat( 'address_', $attribute.id )}
{/if}
{if is_set( $search_button_id )|not}
    {def $search_button_id=concat( 'button_', $attribute.id )}
{/if}
{if is_set( $place_address_div )|not}
    {def $place_address_div=true()}
{/if}
{if is_set( $place_search_button )|not}
    {def $place_search_button=true()}
{/if}
{if is_set( $simple_ids )|not}
    {def $simple_ids=false()}
{elseif $simple_ids}
    {if is_set( $seed )|not}
        {def $seed = rand( 0, 100000 )}    
    {/if}
    {def $attribute=hash( 'id', concat( 'attribute_', $seed ))}
{/if}

{default attribute_base=ContentObjectAttribute}
<div class="block">

<div class="element">

  <div class="block">
    <label>{'Latitude'|i18n('/extension/gmaplocation/datatypes/ezgmaplocation')}:</label>
    {if $simple_ids}
        <input id="gmaplocation_latitude_{$seed}" class="gmaplocation_latitude_{$seed}" type="text" name="gmaplocation_latitude_{$seed}" value="" />
    {else}
        <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_latitude" class="box ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_data_gmaplocation_latitude_{$attribute.id}" value="{$attribute.content.latitude}" />
    {/if}
  </div>
  
  <div class="block">
    <label>{'Longitude'|i18n('/extension/gmaplocation/datatypes/ezgmaplocation')}:</label>
    {if $simple_ids}
        <input id="gmaplocation_longitude_{$seed}" class="gmaplocation_longitude_{$seed}" type="text" name="gmaplocation_longitude_{$seed}" value="" />
    {else}
        <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_longitude" class="box ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_data_gmaplocation_longitude_{$attribute.id}" value="{$attribute.content.longitude}" />
    {/if}    
  </div>

</div>
<div class="element">
<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key={ezini('SiteSettings','GMapsKey')}" type="text/javascript"></script>
<script type="text/javascript">
    function MapControl_{$attribute.id}()
    {literal} 
    {
    {/literal}
        var attribid = '{$attribute.id}'
        var mapid = 'map_{$attribute.id}';
        var addressid = '{$address_div_id}';
        var buttonid = '{$search_button_id}';
        {if $simple_ids}
        var latid = 'gmaplocation_latitude_{$seed}';
        var longid = 'gmaplocation_longitude_{$seed}';        
        {else}
        var latid = 'ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_latitude';
        var longid = 'ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_longitude';
        {/if}
        {literal}
        
        var map = null;
        var geocoder = null;
        var gmapExistingOnload = null;
        var marker = null;
        var me = this;
    
        var showAddress=function( e ) {
          var addrObj = document.getElementById(addressid);
          var address = addrObj.value;
          if (geocoder) {
            geocoder.getLatLng(
              address,
              function(point) {
                if (!point) {
                  // @TODO : stop event propagation here.
                  YAHOO.util.Event.stopEvent( e );
                  e.cancelBubble = true;
                  if ( e.stopPropagation ) e.stopPropagation()
                  if ( YAHOO.ezflgmapblock.GeoSearchState )
                  {
                    YAHOO.ezflgmapblock.GeoSearchState.setState( 'originWasFound', false );
                  }                  
                  alert(address + " not found");
                  addrObj.focus();
                } else {
                  map.setCenter(point, 13);
                  marker = new GMarker(point);
                  map.addOverlay(marker);
                  updateLatLngFields(point);
                  if ( YAHOO.ezflgmapblock.GeoSearchState )
                  {
                    YAHOO.ezflgmapblock.GeoSearchState.setState( 'originWasFound', true );
                  }                  
                }
              }
            );
          }
        };
        /*
        if (window.onload)
        {
                //Hang on to any existing onload function.
                gmapExistingOnload = window.onload;
        }
        */
        
        var updateLatLngFields=function(point){
                  document.getElementById(latid).value = point.lat(); 
                  document.getElementById(longid).value = point.lng(); 
        };
    
        //window.onload=function(ev){
        //Run any onload that we found.
        /*
        if (gmapExistingOnload)
        {
                gmapExistingOnload(ev);
        }
        */
        if (GBrowserIsCompatible()) {
          var startPoint = null;
          var zoom = 0;
          if (document.getElementById(latid).value)
          {
              startPoint = new GLatLng(document.getElementById(latid).value, document.getElementById(longid).value);
              zoom=13;
          }
          else
          {
              startPoint = new GLatLng(0,0);
          }
          
          map = new GMap2(document.getElementById(mapid));
          map.addControl(new GSmallMapControl());
          map.addControl(new GMapTypeControl());
          map.setCenter(startPoint, zoom);
          map.addOverlay(new GMarker(startPoint));
          geocoder = new GClientGeocoder();
          GEvent.addListener(map, "click", function(newmarker, point) {
              map.clearOverlays();
              map.addOverlay(new GMarker(point));
              map.panTo(point);
              updateLatLngFields(point);
              document.getElementById(addressid).value='';
          });
          
          
          YAHOO.util.Event.addListener( buttonid , "click" , showAddress );
          //document.getElementById(buttonid).onclick = showAddress;
        }
    }
    {/literal}
    
    YAHOO.util.Event.onDOMReady( MapControl_{$attribute.id} );
</script>


<div id="map_{$attribute.id}" style="width: 340px; height: 200px"></div>
{if $place_address_div}
<input type="text" id="{$address_div_id}" size="42"/>
{/if}
{if $place_search_button}
<input type="button" id="{$search_button_id}" value="Find Address"/>
{/if}
</div>

<div class="break"></div>
</div>
