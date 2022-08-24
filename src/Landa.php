<?php
namespace Weza;

use Weza\Database;
use Weza\Auth;

class Landa {
    
    /**
     * Delete file
     * 
     * @param   array|string $file
     * @return  true
     */
    public static function notify($message, $user, $type, $class = "branch") {
        $user = Database::table('users')->where('id',$user)->first();
        $data   = array(
            'user' => $user->id,
            'school' => $user->school,
            'branch' => $user->branch,
            'type' => $type,
            'class' => $class,
            'message' => $message
        );
        $insert = Database::table('notifications')->insert($data);
    }

    /**
     * Add timeline activities 
     * 
     * @return true
     */
    public static function timeline($user, $message) {
        $activity = array(
            'user'=>$user,
            'activity'=>$message
        );
        Database::table('timeline')->insert($activity);
        return true;
    }   
    
}
