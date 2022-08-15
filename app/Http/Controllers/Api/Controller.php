<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as BaseController;
use App\Traits\HasJsonResponse;

class Controller extends BaseController
{
    use HasJsonResponse;

    public const PAGE_LIMIT = 25;
    public const PAGE = 1;
}
