<?php
namespace Weza\Controllers;

use Weza\Auth as Authenticate;
use Weza\Database;

class Auth {
    
    /**
     * Get login view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        if (!isset($_GET['secure'])) {
            redirect(url("Auth@get") . "?secure=true");
        }
        $plans = Database::table('plans')->get();
        return view('auth/login',compact('plans'));
    }
    
    
    /**
     * Sign In a user
     * 
     * @return Json
     */
    public function signin() {
        
        $signin = Authenticate::login(input('email'), input('password'), array(
            "rememberme" => true,
            "redirect" => url('Dashboard@get'),
            "status" => "Active"
        ));
        return response()->json($signin);
    }
    
    /**
     * Create an account
     * 
     * @return Json
     */
    public function signup() {
        $user = Database::table(config('auth.table'))->where(config('auth.emailColumn'), input('email'))->first();
        if (!empty($user)) {
            return response()->json(array(
                "status" => "error",
                "title" => "Email Already exists.",
                "message" => "Email Already exists."
            ));
        }

        $user1 = Database::table(config('auth.table'))->where('phone', input('phone'))->first();
        if (!empty($user1)) {
            return response()->json(array(
                "status" => "error",
                "title" => "Phone number Already exists.",
                "message" => "Phone number Already exists."
            ));
        }

        $account_number = self::generateAccountNumber();

        $signup = Authenticate::signup(array(
            "fname" => input('fname'),
            "lname" => input('lname'),
            "email" => input('email'),
            "phone" => input('phone'),
            "company_name" => input('company_name'),
            "company_address" => input('company_address'),
            "account_number" => $account_number,
            "password" => Authenticate::password(input('password')),
            "role" => 'user'

        ), array(
            "authenticate" => true,
            "redirect" => url('Dashboard@get'),
            "uniqueEmail" => input('email')
        ));
        
        return response()->json($signup);
        
    }

    public function notifications(){
        $user = Authenticate::user();
        $messages = Database::table('notifications')->where(['user_id'=>$user->id])->get();
        foreach($messages as $message){
            Database::table('notifications')->where('id',$message->id)->update(array('read'=>'1'));
        }
        $notifications = Database::table('notifications')->where(['user_id'=>$user->id])->get();
        return view('notifications',compact('user','notifications'));
    }

    private function generateAccountNumber(){
        //generate account number
        $account_number=random_int(1000,9999999);
        $acc=Database::table('users')->where('account_number','=',$account_number)->first();
        if(empty($acc)){
            return $account_number;
        }else{
            return self::generateAccountNumber();
        }
    }
    
    public function registerview() {
        return view('auth/register', compact('plans'));
    }


    public function forgot_password_view() {
        return view('auth/forgot-password',compact('plans'));
    }

    /**
     * Forgot password - send reset password email
     * 
     * @return Json
     */
    public function forgotAction() {
        $forgot = Authenticate::forgot(input('email'), env('APP_URL') . "/reset/[token]");
        return response()->json($forgot);
    }
    
    /**
     * Get reset password view
     * 
     * @return \Pecee\Http\Response
     */
    public function resetview($token) {
        return view('auth/reset', array(
            "token" => $token
        ));
    }
    
    /**
     * Reset password
     * 
     * @return Json
     */
    public function reset() {
        $reset = Authenticate::reset(input('token'), input('password'));
        
        return response()->json($reset);
    }
    
    
    /**
     * Sign Out a logged in user
     *
     */
    public function signout() {
        Authenticate::deauthenticate();
        redirect(url("Auth@get"));
        
    }

    public function profile() {
        $user = Authenticate::user();
        return view('profile', compact('user'));        
    }
    
    
}