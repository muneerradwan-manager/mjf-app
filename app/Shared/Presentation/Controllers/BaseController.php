<?php

namespace App\Shared\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Shared\Presentation\Responses\ApiResponse;

class BaseController extends Controller
{
    protected function success($data = null, string $message = 'Success')
    {
        return ApiResponse::success($data, $message);
    }

    protected function error(string $message = 'Error', int $code = 400, $errors = null)
    {
        return ApiResponse::error($message, $code, $errors);
    }
}
