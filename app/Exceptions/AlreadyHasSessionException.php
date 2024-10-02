<?php

namespace App\Exceptions;

use App\Utils\Response;
use Exception;

class AlreadyHasSessionException extends Exception
{
    protected $message = 'Ya tienes una reserva para este evento!';
    protected $code = Response::ALREADY_HAS_SESSION;

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => $this->getMessage()], $this->getCode());
        }

        return parent::render($request);
    }
}