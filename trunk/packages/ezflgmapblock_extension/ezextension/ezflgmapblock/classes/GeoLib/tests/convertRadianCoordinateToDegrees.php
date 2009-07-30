<?php
$radianCoordinates = array( M_PI, M_PI_2, M_PI_4 );

$a = new GeoCalculationsMockup();
$degreesCoordinates = $a->convertRadianCoordinateToDegrees( $radianCoordinates );

/*
 * expected :
 * <code>
array(3) {
  [0]=>
  float(180)
  [1]=>
  float(90)
  [2]=>
  float(45)
}
 * </code>
 */
var_dump( $degreesCoordinates );

?>