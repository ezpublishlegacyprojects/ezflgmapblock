<?php

/**
 * Thrown when the return code of a system execution is not 0
 */
class GeoCalculationsSystemExecutionException extends ezcBaseException
{
    /**
     * Constructs a new GeoCalculationsSystemExecutionException for the
     * shell execution return code $returnCode.
     *
     * @param integer $returnCode Return code of the system execution
     * @param string $command The command which generated a non 0 return code
     * @param string $lastLine The last output line of the command execution. May contains useful feedback.
     */
    public function __construct( $returnCode, $command, $lastLine )
    {
        $message = "The system execution of '{$command}' returned the following exit code: {$returnCode}. The last execution line was : '{$lastLine}'";
        parent::__construct( $message );
    }
}
?>
