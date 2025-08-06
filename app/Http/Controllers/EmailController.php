<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Mail\WelcomeEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendEmail()
    {
        $toEmail = "sumonmti498@gmail.com"; // Default email
        $message = "This is a test email from Laravel application.";
        $subject = "Test Email";
        $user = User::where('email', $toEmail)->first(); // Fetch user by email

       $result = Mail::to($toEmail)->send(new WelcomeEmail($message, $subject,$user));
        return response()->json([
            'status'=>'Success',
            'message'=>'OTP send'
        ]);

       
    }
}
