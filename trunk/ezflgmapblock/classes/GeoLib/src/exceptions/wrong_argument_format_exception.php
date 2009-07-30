<?php

/**
 * Thrown when the format of arguments is not compliant with the method's
 * business logic.
 */
class GeoCalculationsWrongArgumentFormatException extends ezcBaseException
{
    /**
     * Constructs a new GeoCalculationsWrongArgumentFormatException with the
     * error details in $reason.
     *
     * @param string $reason The reason why the argument format is invalid.
     */
    public function __construct( $reason )
    {
        $message = "Wrong argument format \"{$reason}\"";
        parent::__construct( $message );
    }
}
?>
