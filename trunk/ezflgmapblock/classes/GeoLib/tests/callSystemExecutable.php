<?php
$a = new GeoCalculationsMockup();

$command = 'ls';
$arguments = array( '-l' => '',
                    '-h' => '',
                    '-a' => '' );

// The following should list the root of eZ Publish.
try
{
    $output = $a->callSystemExecutable( $command, $arguments );
    var_dump( $output );
    echo "\n";
}
catch( GeoCalculationsSystemExecutionException $e )
{
    echo $e->getMessage() . "\n";
}



$command = 'grep';
$arguments = array( '-ri',
                    'GeoCalculationsMockup',
                    '.' );

// The following should list the root of eZ Publish.
try
{
    $output = $a->callSystemExecutable( $command, $arguments );
    var_dump( $output );
    echo "\n";
}
catch( GeoCalculationsSystemExecutionException $e )
{
    echo $e->getMessage() . "\n";
}




// The following should not work properly, due to an unexisting command, and display a message
// similar to this :
//           sh: line 1: unexisting_command: command not found
//           The system execution of unexisting_command returned the following exit code: 127

$command = 'unexisting_command';

try
{
    $output = $a->callSystemExecutable( $command );
    var_dump( $output );
    echo "\n";
}
catch( GeoCalculationsSystemExecutionException $e )
{
    echo $e->getMessage() . "\n";
}
?>