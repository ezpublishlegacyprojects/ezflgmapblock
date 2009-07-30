var points{$seed} = [];
{def $infoWindowContents=''
     $desc=''}
{foreach $locations as $loc}
    {if $short_descriptive_attribute}
        {set-block variable=$desc}
            {attribute_view_gui attribute=$loc.data_map.$short_descriptive_attribute}
        {/set-block}
        {set $infoWindowContents = concat( '<h2><a href="', $loc.url_alias|ezurl( no ), '">', $loc.name, '</a></h2>', $desc|trim( '\n', '\r', ' ', '\x0B' )|wash( 'javascript' )|explode('\n')|implode( '' ) )|trim( ' ' )}
    {/if}
    {set $infoWindowContents = concat( $infoWindowContents, '<br /><a href="', $loc.url_alias|ezurl( no ), '">', "Read more..."|i18n("design/base"), '</a>' )}

points{$seed}.push( [ new GLatLng( {$loc.data_map.$location_attribute.content.latitude}, {$loc.data_map.$location_attribute.content.longitude} ), '{$infoWindowContents|rawurlencode}' ] );
{/foreach}

YAHOO.ezflgmapblock.GeoSearchState.setState( 'points{$seed}', points{$seed} );