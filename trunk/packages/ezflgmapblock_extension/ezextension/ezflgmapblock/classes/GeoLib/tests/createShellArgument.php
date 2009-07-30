<?php
$qualityParameter = 'quality=%1';
$outputQuality = 200;
var_dump( eZSys::createShellArgument( $qualityParameter, array( '%1' => $outputQuality ) ) );
?>