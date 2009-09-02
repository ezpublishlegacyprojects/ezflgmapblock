{def $locations = fetch( 'content', 'list', hash( 'parent_node_id', $block.custom_attributes.parent_node_id,
                                                  'class_filter_type', 'include',
                                                  'class_filter_array', $block.custom_attributes.classes|explode( ',' ),
                                                  'sort_by', array( 'published', false() ),
                                                  'limit', $block.custom_attributes.limit ) )}
{run-once}
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={ezini('SiteSettings','GMapsKey')}" type="text/javascript"></script>
<script type="text/javascript">
{literal}
    function eZFLGMap_MapView( blockId )
    {
        if (GBrowserIsCompatible()) 
        {
            var startPoint = new GLatLng( 0, 0 ), zoom = 0;

            var map = new GMap2( document.getElementById( 'ezflb-map-' + blockId ) ), 
                bounds = new GLatLngBounds();
            
            map.addControl( new GMapTypeControl() );
            map.addControl( new GLargeMapControl() );
            map.setCenter( startPoint, zoom );
{/literal}
            {if $locations}
            {foreach $locations as $location}
            map.addOverlay(eZFLGMap_Marker( {$location.data_map.location.content.latitude}, {$location.data_map.location.content.longitude}, bounds, '{$location.data_map.location.content.address}' ) );
            {/foreach}
            map.setCenter( bounds.getCenter(), map.getBoundsZoomLevel( bounds ) );
            {/if}
{literal}
        }
    }
    
    function eZFLGMap_Marker( latitude, longitude, bounds, address )
    {
        var point = new GLatLng( latitude, longitude ),
            marker = new GMarker( point );

        GEvent.addListener( marker, 'click', function() {
            marker.openInfoWindowHtml( address );
        } );

        if ( bounds )
            bounds.extend( point );

        return marker;
    }
{/literal}
</script>

<script type="text/javascript">
<!--

if ( window.addEventListener )
    window.addEventListener( 'load', function(){ldelim} eZFLGMap_MapView( '{$block.id}' ) {rdelim}, false);
else if ( window.attachEvent )
    window.attachEvent( 'onload', function(){ldelim} eZFLGMap_MapView( '{$block.id}' ) {rdelim} );

-->
</script>
{/run-once}

<div id="ezflb-map-{$block.id}" style="width: 450px; height: 500px"></div>