<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index(Request $request)
    {
        $info = [
            'name' => config('app.name'),
            'environment' => config('app.env'),
            'status' => 'normal',
            'version' => '1.0.1',
            'ua' => $request->userAgent(),
            'ips' => $request->ips()
        ];

        return view('welcome', compact('info'));
    }
}
