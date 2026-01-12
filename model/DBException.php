<?php

/**
 * Custom Database Exception class for handling database-related errors.
 * Compatible with PHP 5.4.7+
 */
class DBException extends Exception
{
    /**
     * Constructor for DBException
     * @param string $message The exception message
     * @param int $code The exception code (default: 0)
     * @param Exception $previous The previous exception (default: null)
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        // Call parent constructor
        parent::__construct($message, $code, $previous);
    }

    /**
     * String representation of the exception
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
?>