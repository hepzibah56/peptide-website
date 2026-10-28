<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index(): Response
    {
        return response()->view('contact.index');
    }

    public function store(ContactRequest $request): Response
    {
        Mail::to(config('shop.email'))->send(new \App\Mail\ContactMail($request->validated()));

        return response()->view('contact.thankyou')->header('Cache-Control', 'no-store');
    }
}