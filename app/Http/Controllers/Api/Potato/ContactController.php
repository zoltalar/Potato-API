<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\ContactRequest;
use App\Models\Admin;
use App\Notifications\Contact;

class ContactController extends Controller
{
    public function contact(ContactRequest $request)
    {
        foreach (Admin::all() as $admin) {
            $admin->notify(new Contact($request->validated()));
        }

        return response()->json($request->validated());
    }
}
