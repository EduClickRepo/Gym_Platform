<?php

namespace App\Exceptions;

use App\Utils\Response;
use Exception;
use JetBrains\PhpStorm\Pure;

class PenalizedException extends Exception
{
    protected $code = Response::USER_PENALIZED;

    #[Pure] public function __construct(string $classType, string $toDate)
    {
        parent::__construct('Tienes una penalización por inasistencia a clase de ' . $classType . ' hasta el: ' . $toDate);
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => $this->getMessage()], $this->getCode());
        }

        return parent::render($request);
    }
}