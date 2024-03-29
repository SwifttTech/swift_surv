<?php

use Weza\Auth;
use Weza\Config;
use Weza\Container;
use Weza\Database;
use Weza\FS;
use Weza\Router;
use Weza\Session;
use Weza\Str;

if(! function_exists('asset')) {
    /**
     * Generate a valid asset url
     * 
     * @param   string  $url
     * @return  mixed
     */
    function asset($url) {
        return substr(url($url), 0, -1);
    }
}
if (! function_exists('back')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function back()
    {
        return redirect(request()->getReferer());
    }
}
if (! function_exists('money')) {
    /**
     * Return formated money with currency.
     *
     * @param  int|decimal  $number
     * @return string
     */
    function money($number)
    {
        return moneyFormat($number, 'kes'); 
    }
}
if (! function_exists('currency')) {
    /**
     * Return school currency symbol.
     *
     * @param  int|decimal  $number
     * @return string
     */
    function currency()
    {
        // $school = School::setup();
        $currency = new Gerardojbaez\Money\Currency('kes');
        return $currency->getSymbol(); 
    }
}

if (! function_exists('isAllowed')) {
    /**
     * determine if a user has permissions to access a feature.
    */
    function isAllowed($feature)
    {
        // $school = School::setup();
        $user = Auth::user();
        $role = Database::table('roles')->where(['id'=>$user->role_id])->first();
        if($role->$feature == '1'){
            return true;
        }else{
            return false;
        }
    }
}


if (! function_exists('notificationList')) {
    /**
     * Return school currency symbol.
     *
     * @param  int|decimal  $number
     * @return string
     */
    function notificationList()
    {
        // $school = School::setup();
        $user = Auth::user();
        $notifications = Database::table('notifications')->where(['user_id'=>$user->id, 'read'=>'0'])->get();
        return $notifications;
    }
}

if(! function_exists('config')) {
    /**
     * Get a config value
     * 
     * @param   string  $str
     * @param   mixed   $value
     * @return  mixed
     */
    function config($str, $value = null) {
        if (is_null($value)) {
            return Config::get($str);
        }else {
            return Config::set($str, $value);
        }
    }
}

if(! function_exists('container')) {
    /**
     * Get/Set a config value
     * 
     * @param   string  $key
     * @param   mixed   $value
     * @return  mixed
     */
    function container($key, $value = null) {
        $container = Container::getInstance();
        if ( is_null($value) ) {
            return $container->get($key);
        } else {
            return $container->set($key, $value);
        }
    }
}

if (! function_exists('cookie')) {
    /**
     * Get/Set a cookie
     * 
     * @param   string  $key
     * @param   mixed   $value
     * @param   float   $days
     * @return  mixed
     */
    function cookie($key, $value = null, $days = 1) {
        if ( is_null($value) ) {
            return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
        } else {
            return setcookie($key, $value, time() + (86400 * $days), '/');
        }
    }
}

if (! function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env($key, $default = null) {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch ( strtolower($value) ) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        if ( strlen($value) > 1 && Str::startsWith($value, '"') && Str::endsWith($value, '"') ) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

if (! function_exists('hash_compare')) {
    /**
     * Compare two string hashes
     * 
     * @param   string  $a
     * @param   string  $a
     * @return  boolean
     */
    function hash_compare($a, $b) {
        if ( !is_string($a) || !is_string($b) ) { 
            return false; 
        } 
        
        $len = strlen($a); 
        if ($len !== strlen($b)) { 
            return false; 
        } 

        $status = 0; 
        for ($i = 0; $i < $len; $i++) { 
            $status |= ord($a[$i]) ^ ord($b[$i]); 
        } 
        return $status === 0; 
    }
}
if(! function_exists('generatePassword')){
    /**
     * Generate a random password
     * 
     * @return  string
    */
    function generatePassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
}
if (! function_exists('session')) {
    /**
     * Get the current session
     * 
     * @param   mixed   $key
     * @param   mixed   $value
     * @return  mixed
     */
    function session($key = null, $value = null) {
        $session = container(Session::class);
        if ( is_null($key) ) {
            return $session;
        } else if ( is_null($value) ) {
            return $session->get($key);
        } else {
            $session->put($key, $value);
        }        
    }
}



if (! function_exists('responder')) {
    /**
     * Return Json response
     * 
     * @param   mixed   $key
     * @param   mixed   $value
     * @return  mixed
     */
    function responder($status, $title, $message, $callback = null, $notify = true, $notifyType = null, $callbackTime = "onconfirm") {
        $response = array(
                "status" => $status,
                "title" => $title,
                "message" => $message
            );
        if (!empty($callback)) {
            $response["callback"] = $callback;
        }
        if (!$notify) {
            $response["notify"] = false;
        }
        if (isset($notifyType)) {
            $response["notifyType"] = $notifyType;
        }
        if ($callbackTime == "instant") {
            $response["callbackTime"] = $callbackTime;
        }
        return $response;     
    }
}
if (! function_exists('escape')) {
    /**
     * Return an escaped string
     * 
     * @param   string   $string
     * @return  string
     */
    function escape($string) {
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        $string = htmlentities($string, ENT_QUOTES);
        return $string;     
    }
}
if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (! function_exists('view')) {
    /**
     * Return a html page view
     * 
     * @param   string  $name
     * @param   array   $data
     * @return  string
     */
    function view($name = 'errors.404', array $data = []) {
        $general_path = FS::disk('views')->path(str_replace('.', '/', $name));
        $HTML_path = "{$general_path}.html";
        $PHP_path = "{$general_path}.php";
        $text_path = "{$general_path}.txt";

        ob_start();
        if (file_exists($PHP_path)) {
            $general_path = $PHP_path;
        } else if (file_exists($HTML_path)) {
            $general_path = $HTML_path;
        } else if (file_exists($text_path)) {
            $general_path = $text_path;
        } else {
            $general_path = FS::path('errors/404.php');
        }
        include $general_path;

        $search_n_replace = [
            '/{{/'                      => '<?= ',
            '/}}/'                      => '; ?>',
            '/\@include\s*\((.*)\)/'         => '<?= view( $1, $s_v_data ); ?>',
            '/\@for(\w*)\s*(\(.*\))/'   => '<?php for$1 $2 { ?>',
            '/\@if\s*(\(.*\))/'         => '<?php if $1 { ?>',
            '/\@elseif\s*(\(.*\))/'     => '<?php } else if $1 { ?>',
            '/\@else/'                  => '<?php } else { ?>',
            '/\@end\w+/'                => '<?php } ?>',
        ];
        $globals = ['<?php global $s_v_data'];
        global $s_v_data;
        $s_v_data = $data;
        foreach($data as $var => $val) {
            global ${$var};
            ${$var} = $val;
            array_push($globals, ", \${$var}");
        }
        array_push($globals, "; ?>\n");
        $view = preg_replace(
            array_keys($search_n_replace),
            array_values($search_n_replace),
            implode('', $globals) . ob_get_contents() . "\n<?php return;\n"
        );
        ob_clean();
        $cache_filename = 'framework/views/' . md5($name) . '.php';
        FS::disk('storage')->save($cache_filename, $view);
        include FS::path($cache_filename);

        return ob_get_clean();
    }
}

if (! function_exists('__')) {
    /**
     * Get the translated value of the set language
     * 
     * @param   string  $name
     * @return  string
     */
    function __($name) {
        $dot_keys = explode('.', $name);
        $locale = config('app.locale.default');
        $dir = config('filesystem.disks.language');
        $PHP_path = "{$dir}/{$locale}/{$dot_keys[0]}.php";
        if (file_exists($PHP_path)) {
            $value = include $PHP_path;
            if(count($dot_keys) > 1) {
                for($x = 1; $x < count($dot_keys); $x++) {
                    $value = $value[$dot_keys[$x]];
                }
            }
            return $value;
        } else {
            return $name;
        }
    }
}
