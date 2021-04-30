<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class ApiController extends Controller
{
    use ApiResponse;

    /**
     * ユーザ情報
     * 
     * @param Request $request
     * @return JsonResponse
     */
    protected function user(Request $request)
    {
        return $this->success($request->user());
    }
}