<?php
namespace Weza\Middleware;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Weza\Auth;

class Authenticate implements IMiddleware {

    /**
     * Redirect the user if they are unautenticated
     * 
     * @param   \Pecee\Http\Request $request
     * @return  \Pecee\Http]Request
     */
    public function handle(Request $request) {
        Auth::remember();
        
        if (Auth::check()) {
            $request->user = Auth::user();
            date_default_timezone_set('Africa/Nairobi');
            // Set the locale to the user's preference
            config('app.locale.default', $request->user->{config('auth.locale')});
            
        } else {
            $request->setRewriteUrl(url('Auth@get'));
        }
            return $request;
        
        }
}
