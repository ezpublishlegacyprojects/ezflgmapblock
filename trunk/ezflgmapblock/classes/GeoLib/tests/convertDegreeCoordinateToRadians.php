<?php
$degreesCoordinates = array( 180, 90, 45 );

$a = new GeoCalculationsMockup();
$radianCoordinates = $a->convertDegreeCoordinateToRadians( $degreesCoordinates );

/*
 * expected :
 * <code>
 *   array( M_PI, M_PI_2, M_PI_4 )
 * </code>
 */
var_dump( $radianCoordinates );
?>