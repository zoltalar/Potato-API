<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\ContactRequest;
use App\Jobs\SendContactJob;

class ContactController extends Controller
{
    public function contact(ContactRequest $request)
    {
        $this->dispatch(new SendContactJob($data = $request->validated()));

        return response()->json($data);
    }
}
