<?php


namespace App\Http\Forms\Exceptions;


use Throwable;

abstract class FormException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
