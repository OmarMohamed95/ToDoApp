<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UnauthorizedTaskUser extends AccessDeniedHttpException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
