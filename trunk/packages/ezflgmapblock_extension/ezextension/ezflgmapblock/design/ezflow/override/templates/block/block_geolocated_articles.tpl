{if is_set( $block.custom_attributes.node_id )}
<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key={ezini('SiteSettings','GMapsKey')}" type="text/javascript"></script>
	{def $geolocated_articles = fetch( 'content', 'list', hash( 'parent_node_id', $block.custom_attributes.node_id,
																'class_filter_type', 'include',
																'class_filter_array', array( 'article' ),
																'sort_by', array( 'modified', false() )
																 ))
		 $articles_folder = fetch( 'content', 'node', hash( 'node_id', $block.custom_attributes.node_id ))																 	
		 $seed = rand( 0, 10000 )
	}
<script type="text/javascript">
<!--

	var points = [];
	
	{* retrieve all geo-locations *}
	{def $infoWindowContents=''
		 $intro=''}
	{foreach $geolocated_articles as $article}
		{set-block variable=$intro}
			{attribute_view_gui attribute=$article.data_map.intro}
		{/set-block}
		{set $infoWindowContents = concat( '<h2><a href="', $article.url_alias|ezurl( no ), '">', $article.name, '</a></h2>', $intro|trim( '\n', '\r', ' ', '\x0B' )|wash( 'javascript' )|explode('\n')|implode( '' ) )|trim( ' ' )}
		{set $infoWindowContents = concat( $infoWindowContents, '<br /><a href="', $article.url_alias|ezurl( no ), '">', "Read more..."|i18n("design/base"), '</a>' )}
		
		points.push( [ new GLatLng( {$article.data_map.location.content.latitude}, {$article.data_map.location.content.longitude} ), '{$infoWindowContents}' ] );
	{/foreach}
-->
</script>

<!-- BLOCK: START -->
<div class="block-type-2items">

<div class="border-box block-style1-box-outside">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<!-- BLOCK BORDER INSIDE: START -->

<div class="border-box block-style1-box-inside">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<!-- BLOCK CONTENT: START -->

<div class="class-article">
    {* 
    <div class="attribute-header">
        <h2><a href={$geolocated_article.url_alias|ezurl()}>{$geolocated_article.name}</a></h2>
    </div>
    *}
    
    <div class="attribute-header">
        <h2><a href={$articles_folder.url_alias|ezurl()}>{$articles_folder.name}</a></h2>
    </div>
    
    <div class="attribute-long">
    <ul>
	{foreach $geolocated_articles as $key => $article}
		<li><a onclick="map.panTo( points[{$key}][0] );map.setCenter( points[{$key}][0], 13 );map.openInfoWindowHtml( points[{$key}][0], points[{$key}][1] );">{$article.name}</a></li>
	{/foreach}
	</ul>    
    </div>
    
    <div class="separator"></div>
    
    {* <div class="attribute-long">{attribute_view_gui attribute=$geolocated_article.data_map.location type='large'}</div> *}
    <div class="attribute-long">
    	<div id="map_{$seed}" style="width: 240px; height: 150px"></div>
    </div>

	{*
    <div class="attribute-short">
        {attribute_view_gui attribute=$geolocated_article.data_map.body}
    </div>
    *}
</div>

<!-- BLOCK CONTENT: END -->

</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

<!-- BLOCK BORDER INSIDE: END -->


</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

</div>
<!-- BLOCK: END -->

<script type="text/javascript">
<!--
	
    var mapid = 'map_{$seed}';
    var eZIconURL = "{'design/standard/images/favicon.ico'|ezroot( 'no', 'full' )}";
    {literal}
    
    var map = null;
    var gmapExistingOnload_ = null;

    if (window.onload)
    {
            //Hang on to any existing onload function.
            gmapExistingOnload_ = window.onload;
    }

    window.onload=function(ev){
        //Run any onload that we found.
        if ( gmapExistingOnload_ )
        {
        	gmapExistingOnload_(ev);
        }
        if (GBrowserIsCompatible()) {
          var GMapOptions_ = {};
          GMapOptions_ = { size : new GSize( 440, 600 ) };
          
          map = new GMap2( document.getElementById( mapid ), GMapOptions_ );
          map.addControl( new GSmallMapControl() );
          map.setCenter( points[0][0] );
          for ( var i=0; i < points.length; i++ )
          {
          	map.addOverlay( createMarker( points[i][0], points[i][1] ) );	
          }
          setTimeout( 'map.setZoom( 13  ); map.openInfoWindowHtml( points[0][0], points[0][1] );', 4000);
          // map.openInfoWindowHtml( points[0][0], points[0][1] );
        }
    };
    
    // Creates a marker at the given point
    // Clicking the marker will hide it
    function createMarker( latlng, html ) {
      var baseIcon = new GIcon();
      baseIcon.shadow = "http://www.google.com/mapfiles/shadow50.png";
      baseIcon.iconSize = new GSize( 16, 16);
      baseIcon.shadowSize = new GSize(37, 34);
      baseIcon.iconAnchor = new GPoint(9, 34);
      baseIcon.infoWindowAnchor = new GPoint(9, 2);
      baseIcon.infoShadowAnchor = new GPoint(18, 25);
      baseIcon.image = eZIconURL;
      
      markerOptions = { icon:baseIcon };            
      var marker = new GMarker( latlng, markerOptions );
      marker.value = Math.random();
      GEvent.addListener( marker, "click", function() { map.openInfoWindowHtml( latlng, html ); } );
      return marker;
	}    

    {/literal}
-->
</script>

{/if}