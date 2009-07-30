<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key={ezini('SiteSettings','GMapsKey')}" type="text/javascript"></script>
{def $seed = rand( 0, 100000 )
     $infoWindowContents=''
     $object=$attribute.object}

<script type="text/javascript">
var MapViewer_{$attribute.id}{$seed} = function()
{literal}
{
{/literal}
    var attribid = {$attribute.id}
    var mapid = 'map_{$attribute.id}';
    {if is_set($attribute.content.latitude)}
    var lat = {$attribute.content.latitude};
    var long = {$attribute.content.longitude};
    {else}
    var lat = 0.0;
    var long = 0.0;
    {/if}
    {literal}
    
    var map = null;
    var geocoder = null;
    var gmapExistingOnload = null;
    var marker = null;
    if (GBrowserIsCompatible()) 
    {
        var startPoint = new GLatLng(0,0);
        var zoom = 0;
        if(lat && long)
        {
          startPoint = new GLatLng(lat,long);
          zoom=13
        }
        map = new GMap2(document.getElementById(mapid));
        map.addControl(new GSmallMapControl());
        map.setCenter(startPoint, zoom);
        marker = new GMarker(startPoint);
        {/literal}
	        {set $infoWindowContents = concat( '<h5><a href="', $object.main_node.url_alias|ezurl( no ), '">', $object.name, '</a></h5>' )|trim( ' ' )}
	        {set $infoWindowContents = concat( $infoWindowContents, '<a href="', $object.main_node.url_alias|ezurl( no ), '">', "Read more..."|i18n("design/base"), '</a>' )} 
        {literal}                
        GEvent.addListener(marker, "click", function() {
            marker.openInfoWindowHtml( '{/literal}{$infoWindowContents}{literal}' );
        });
        map.addOverlay(marker);

    }
}
{/literal}
YAHOO.util.Event.onDOMReady( MapViewer_{$attribute.id}{$seed} );
</script>

<div id="map_{$attribute.id}" style="width: 240px; height: 150px"></div>