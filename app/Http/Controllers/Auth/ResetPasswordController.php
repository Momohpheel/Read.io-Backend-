<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Traits\Response;
use Illuminate\Http\Request;
use App\Models\User;
class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords,Response;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function changePassword(Request $request){
        $validated = $request->validate([
            'old_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);
        $user = User::find(auth()->user()->id);
            if ($user->password == $validated['old_password']){
                $user->password = Hash::make($request->password);
                $user->save();

               return $this->success("Password Chaneg Successfull", 200);
            }else{
                return $this->error("Old Password is Wrong", 400);
            }

    }
     /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return $this->success(trans($response), "Password Reset Successfull", 200);
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return $this->success(trans($response), "Password Reset Failed", 400);
    }

}
