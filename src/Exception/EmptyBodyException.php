<?php


namespace App\Exception;
use Throwable;

class EmptyBodyException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('the body of the put/post cannot by empty', $code, $previous);
    }

}