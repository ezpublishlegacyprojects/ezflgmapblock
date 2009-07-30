{if is_set( $locations )|not }
{def $locations= array()}
{/if}
{if is_set( $size )|not }
{def $size= array(400, 400)}
{/if}
{if is_set($show_popups_on_page)|not}
{def $show_popups_on_page=false()}
{/if}
{if is_set($map_id)|not}
{def $map_id='map'}
{/if}
{if is_set($map_type)|not}
{def $map_type = 'G_NORMAL_MAP'}
{/if}
{if is_set($popup_view)|not}
{def $popup_view = 'line'}
{/if}
{if is_set($location_attribute)|not}
{def $location_attribute = 'location'}
{/if}
{if is_set( $short_descriptive_attribute )|not }
{def $short_descriptive_attribute=false()}
{/if}

{def $seed = rand( 0, 100000 )}
{set $map_id=concat( $map_id, $seed )}

<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key={ezini('SiteSettings','GMapsKey')}" type="text/javascript"></script>

<div id="ezfind_geosearch_{$seed}">
    <h4>{'Find points of interest'|i18n( 'extension/ezflgmapblock/geosearch' )}</h4>
    <input type="text" size="40" name="ezfind_geosearch_origin_{$seed}" id="ezfind_geosearch_origin_{$seed}" value="{'Where are you?'|i18n( 'extension/ezflgmapblock/geosearch' )}">
    
    <span id="ezfind_geosearch_submit_container_{$seed}" name="ezfind_geosearch_submit_container_{$seed}">
        <input type="submit" id="ezfind_geosearch_submit_{$seed}" name="ezfind_geosearch_submit_{$seed}" value="Search">
    </span>
    <br />
    <input type="text" size="15" name="ezfind_geosearch_radius_{$seed}" id="ezfind_geosearch_radius_{$seed}" value="{'How far from you?'|i18n( 'extension/ezflgmapblock/geosearch' )}"> ( {'Meters'|i18n( 'extension/ezflgmapblock/geosearch' )} )
    <br />
    <h5><div style="padding: 2px;" id="ezfind_geosearch_feedback_{$seed}"></div></h5>    
    <hr />
    <div style="display: none;">
    {include uri="design:content/datatype/edit/ezgmaplocation.tpl"
             address_div_id=concat( 'ezfind_geosearch_origin_', $seed )
             search_button_id=concat( 'ezfind_geosearch_submit_', $seed )
             place_address_div=false()
             place_search_button=false()
             simple_ids=true()
             seed=$seed}
    </div>
</div>

<div id="gmap_material_{$seed}" name="gmap_material_{$seed}">
{include uri="design:parts/gmap_material.tpl" 
         seed=$seed
         locations=$locations
         map_id=$map_id
         size=$size
         show_popups_on_page=$show_popups_on_page
         location_attribute=$location_attribute}
</div>

<script type="text/javascript">
    var mapid{$seed} = '{$map_id}';    
    var map{$seed} = null;
    var geocoder = null;
    //var gmapExistingOnload = null;
    var marker = null;

    {literal}
    
    function createMarker( lat, lng, info, bounds, icon)
    {
      var point = new GLatLng(lat, lng);
      var marker = new GMarker( point, icon );
      GEvent.addListener(marker, "click", function() {
        marker.openInfoWindowHtml(info);
      });
      if (bounds)
      {
          bounds.extend(point);
      }
      return marker;      
    }


    var load{/literal}{$seed}{literal} = function(ev){
        if (GBrowserIsCompatible()) {
          map{/literal}{$seed}{literal} = new GMap2(document.getElementById(mapid{/literal}{$seed}{literal}));
          map{/literal}{$seed}{literal}.addControl(new GMapTypeControl());
          map{/literal}{$seed}{literal}.addControl(new GLargeMapControl());
          map{/literal}{$seed}{literal}.setCenter(new GLatLng(0,0), 0);
          var bounds = new GLatLngBounds();
    {/literal}
    
	{def $location_data = null}
	{foreach $locations as $index=>$location}
	    {if is_set($location.name)}
	    {set $location_data = $location.data_map[$location_attribute].content}
	    {else}
	    {set $location_data = $location}
	    {/if}
          var popupwindow_{$index}=unescape( points{$seed}[{$index}][1] );
          map{$seed}.addOverlay(createMarker({$location_data.latitude},{$location_data.longitude},popupwindow_{$index}, bounds));
    {/foreach}
    
          map{$seed}.setMapType({$map_type});
          {if is_set($center)}
          var center = new GLatLng({$center[0]},{$center[1]});
          {else}
          var center = bounds.getCenter();
          {/if}
          {if is_set($zoom)}
          var zoom = {$zoom};
          {else}
          var zoom = map{$seed}.getBoundsZoomLevel(bounds);
          {/if}
          map{$seed}.setCenter(center,zoom);
    {literal}
       
        }
    };
    {/literal}
    
    YAHOO.util.Event.onDOMReady( load{$seed} );
</script>
<script type="text/javascript">
<!--
// Create the geo search manager, and initiate it : 
var arguments{$seed} = {ldelim}
                baseURL : '{'/'|ezurl( 'no', 'full' )}',
                searchButtonId : 'ezfind_geosearch_submit_container_{$seed}',
                gmapMaterialId : 'gmap_material_{$seed}',
                map : map{$seed},
                mapId : '{$map_id}',
                seed : {$seed},
                size : [{$size[0]}, {$size[1]}],
                showPopupsOnPage : {$show_popups_on_page},
                latitudeElementId : 'gmaplocation_latitude_{$seed}',
                longitudeElementId : 'gmaplocation_longitude_{$seed}',
                radiusElementId : 'ezfind_geosearch_radius_{$seed}',
                originElementId : 'ezfind_geosearch_origin_{$seed}',
                locationAttribute : '{$location_attribute}',
                feedbackId : 'ezfind_geosearch_feedback_{$seed}'
                {if $short_descriptive_attribute},
                shortDescriptiveAttribute : '{$short_descriptive_attribute}' 
                {/if}
                {rdelim};
var geoSearchManager{$seed} = new YAHOO.ezflgmapblock.GeoSearchManager();
{* geoSearchManager{$seed}.init( arguments{$seed} ); *}

var loadManager{$seed} = function () 
                                    {ldelim} 
                                        geoSearchManager{$seed}.init( arguments{$seed} );
                                    {rdelim};
YAHOO.util.Event.onDOMReady( loadManager{$seed} );
-->
</script>