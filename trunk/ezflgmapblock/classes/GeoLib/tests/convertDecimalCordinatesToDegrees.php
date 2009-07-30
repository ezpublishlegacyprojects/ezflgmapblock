<?php
$a = new GeoCalculationsMockup();

/**
 * Expected result :
 * Exception raised ( GeoCalculationsWrongArgumentFormatException ), with text :
 *   Wrong argument format "Decimal coordinates must be passed as {latitude, longitude} pairs."
 */
$decimalCoordinates = array( 33.3 );

try
{
    $output = $a->convertDecimalCoordinatesToDegrees( $decimalCoordinates );
    var_dump( $output );
    echo "\n";
}
catch( Exception $e )
{
    echo $e->getMessage() . "\n";
}



/*
 * Expected result :
    <code>
    array(2) {
      [0]=>
      string(12) "33d18'00.0"N"
      [1]=>
      string(13) "044d24'00.0"E"
    }
    </code>
 */
$decimalCoordinates = array( 33.3, 44.4 );

try
{
    $output = $a->convertDecimalCoordinatesToDegrees( $decimalCoordinates );
    var_dump( $output );
    echo "\n";
}
catch( Exception $e )
{
    echo $e->getMessage() . "\n";
}




/**
 * Expected result :
 * Exception raised ( GeoCalculationsSystemExecutionException ), with text :
 *   The system execution of 'echo  332  2342343 | GeoConvert  -d' returned the following exit code: 1. The last execution line was : 'ERROR: Latitude 332d not in [-90d, 90d]'
 */
$decimalCoordinates = array( 332, 2342343 );

try
{
    $output = $a->convertDecimalCoordinatesToDegrees( $decimalCoordinates );
    var_dump( $output );
    echo "\n";
}
catch( Exception $e )
{
    echo $e->getMessage() . "\n";
}
?>