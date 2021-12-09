<?php

namespace App\Http\Controllers;
use Twilio\Rest\Client;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function sendSmsNotificaition()
    {
       // Your Account SID and Auth Token from twilio.com/console
       $account_sid = 'AC00475164a5d721069d8554325c879411';
       $auth_token = 'b2c7ddfd2240df32986bf6f8bdbd1ebc';
       // In production, these should be environment variables. E.g.:
       // $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]

       // A Twilio number you own with SMS capabilities
       $twilio_number = "+34662032229";

       $client = new Client($account_sid, $auth_token);
       $client->messages->create(
           // Where to send a text message (your cell phone?)
           '+34605652415',
           [
            'from' => $twilio_number,
            'body' => 'I sent this message in under 10 minutes!'
            ]
        );
    }
}
