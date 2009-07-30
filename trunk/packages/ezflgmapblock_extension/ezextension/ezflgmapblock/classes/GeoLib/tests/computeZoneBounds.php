<?php
$a = new GeoCalculationsMockup();

$origin = array( M_PI, M_PI_2 );
$radius = 1000; // 1km

/*
 * expected :
 * <code>
array(4) {
  [0]=>
  array(2) {
    [0]=>
    string(22) "3.14143569258095509020"
    [1]=>
    float(1.5707963267949)
  }
  [1]=>
  array(2) {
    [0]=>
    string(22) "3.14174961459864490980"
    [1]=>
    float(1.5707963267949)
  }
  [2]=>
  array(2) {
    [0]=>
    float(3.1415926535898)
    [1]=>
    string(22) "1.57095328780374490980"
  }
  [3]=>
  array(2) {
    [0]=>
    float(3.1415926535898)
    [1]=>
    string(22) "1.57063936578605509020"
  }
}
 * </code>
 */
try
{
    $zoneBounds = $a->computeZoneBounds( $origin, $radius );
    var_dump( $zoneBounds );
    echo "\n";
}
catch( Exception $e )
{
    echo $e->getMessage() . "\n";
}




$origin = array( M_PI );
$radius = 1000; // 1km

/*
 * Exception expected :
 *    Wrong argument format "The origin point must be passed as a {latitude, longitude} pair (array)."
 */
try
{
    $zoneBounds = $a->computeZoneBounds( $origin, $radius );
    var_dump( $zoneBounds );
    echo "\n";
}
catch( Exception $e )
{
    echo $e->getMessage() . "\n";
}




$origin = array( M_PI, M_PI_2 );
$radius = -1000;

/*
 * Exception expected :
 *    Wrong argument format "The radius must be a positive floating point value, in meters."
 */
try
{
    $zoneBounds = $a->computeZoneBounds( $origin, $radius );
    var_dump( $zoneBounds );
    echo "\n";
}
catch( Exception $e )
{
    echo $e->getMessage() . "\n";
}
?>