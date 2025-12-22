<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

abstract class BaseController
{
    public function __construct(protected Request $request, protected Response $response)
    {
    }
}
