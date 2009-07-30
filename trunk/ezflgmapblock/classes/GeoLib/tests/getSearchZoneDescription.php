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
array(4) {
  [0]=>
  array(2) {
    [0]=>
    string(23) "45.75742237966450710994"
    [1]=>
    string(22) "4.83444879999999999992"
  }
  [1]=>
  array(2) {
    [0]=>
    string(23) "45.75922102033549288925"
    [1]=>
    string(22) "4.83444879999999999992"
  }
  [2]=>
  array(2) {
    [0]=>
    string(23) "45.75832169999999999959"
    [1]=>
    string(22) "4.83534812033549288957"
  }
  [3]=>
  array(2) {
    [0]=>
    string(23) "45.75832169999999999959"
    [1]=>
    string(22) "4.83354947966450711027"
  }
}
 * </code>
 */
try
{
    $searchZoneDescription = $a->getSearchZoneDescription( $origin, $radius );
    var_dump( $searchZoneDescription );
    echo "\n";
}
catch( Exception $e )
{
    echo $e->getMessage() . "\n";
}


$origin = array( $lat, $long );
$radius = -100; // in meters

/*
 * expected :
 * Exception :
 *    Wrong argument format "The radius must be a positive floating point value, in meters."
 */
try
{
    $searchZoneDescription = $a->getSearchZoneDescription( $origin, $radius );
    var_dump( $searchZoneDescription );
    echo "\n";
}
catch( Exception $e )
{
    echo $e->getMessage() . "\n";
}


$origin = array( $lat );
$radius = 100; // in meters

/*
 * expected :
 * Exception :
 *    Wrong argument format "The origin point must be passed as a {latitude, longitude} pair (array)."
 */
try
{
    $searchZoneDescription = $a->getSearchZoneDescription( $origin, $radius );
    var_dump( $searchZoneDescription );
    echo "\n";
}
catch( Exception $e )
{
    echo $e->getMessage() . "\n";
}

?>