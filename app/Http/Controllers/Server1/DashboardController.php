<?php

namespace App\Http\Controllers\Server1;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class DashboardController extends ApiController
{
    public function index(Request $request)
    {
        return $this->success($request->user());
    }
}
