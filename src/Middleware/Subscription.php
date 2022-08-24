<?php
namespace Weza\Middleware;

use Pecee\Http\Middleware\IMiddleware;
use Weza\Middleware\School;
use Pecee\Http\Request;
use Weza\Auth;

class Subscription implements IMiddleware {

    /**
     * Redirect the user if they are unautenticated
     * 
     * @param   \Pecee\Http\Request $request
     * @return  \Pecee\Http]Request
     */
    public static function handle(Request $request) {
        Auth::remember();
        
        if (Auth::user()->subscription_status == '1') {
            $request->user = Auth::user();
            $school = School::setup();
            date_default_timezone_set($school->timezone);
            // Set the locale to the user's preference
            config('app.locale.default', $request->user->{config('auth.locale')});
            
        } else {
            $request->setRewriteUrl(url('Invoice@paySubscription'));
        }
            return $request;
        
        }
}
