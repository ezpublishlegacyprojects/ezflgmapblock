<?php
$a = new GeoCalculations();

// 26 rue de la rŽpublique 69002 Lyon FRANCE.
$lat  = 45.7583217;
$long = 4.8344488;

$origin = array( $lat, $long );
$radius = 100; // in meters

/*
 * expected :
 * <code>
    array(2) {
      ["latitude-filter"]=>
      string(52) "[45.75742237966450710994 TO 45.75922102033549288925]"
      ["longitude-filter"]=>
      string(50) "[4.83354947966450711027 TO 4.83534812033549288957]"
    }
 * </code>
 */
try
{
    $searchZoneSolrFilters = $a->generateSolrSearchZoneFilters( $a->getSearchZoneDescription( $origin, $radius ) );
    var_dump( $searchZoneSolrFilters );
    echo "\n";
}
catch( Exception $e )
{
    echo $e->getMessage() . "\n";
}

?>