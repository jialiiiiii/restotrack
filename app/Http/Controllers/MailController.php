<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MailController extends Controller
{
    public static function verifyEmail($to, $token)
    {

        Mail::send(
            ['html' => 'emails.verifyemail'],
            ['url' => 'http://127.0.0.1:8000/customers/verify?token=' . $token],
            function ($message) use ($to) {
                $message->to($to)->subject('Verify Your Email');
                $message->from(config('mail.mailers.smtp.username'), 'Perfecto Pizzas');
            })
        ;

    }
}