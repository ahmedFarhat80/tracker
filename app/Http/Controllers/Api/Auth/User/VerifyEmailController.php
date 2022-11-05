<?php

namespace App\Http\Controllers\Api\Auth\User;

use Illuminate\Foundation\Auth\sendVerificationRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Auth\Events\Verified;
use Illuminate\Http\Request;

use App\Models\Users;

class VerifyEmailController extends Controller
{
    // public function __invoke(Request $request): RedirectResponse
    // {
    //     $user = User::find($request->route('id'));

    //     if ($user->hasVerifiedEmail()) {
    //         // return redirect(env('FRONT_URL') . '/email/verify/already-success');
    //         return "already cerified";
    //     }

    //     if ($user->markEmailAsVerified()) {
    //         event(new Verified($user));
    //     }

    //     return redirect(env('FRONT_URL') . '/email/verify/success');
    // }


    /** */
    public function sendVerificationEmail(Request $request){
        if($request->user()->hasVerifiedEmail()){
            return[
                'message'   => 'Already verified',
            ];
        }

        $request->user()->sendEmailVerificationNotification();
        
        return[
            'status'   => 'Verification-link-sent',
        ];
    }

    public function verify(EmailVerificationRequest $request){
        if($request->user()->hasVerifiedEmail()){
            return[
                'message'   => 'Email already verified',
            ];
        }

        if ($request()->user()->markEmailAsVerified()) {
            event(new Verified($reqeust->user()));
        }

        return [
            'message' => 'Email has been verified',
        ];
    }
}
