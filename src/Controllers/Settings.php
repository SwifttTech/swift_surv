<?php
namespace Weza\Controllers;

use Weza\Auth;
use Weza\File;
use Weza\Database;
use DotEnvWriter\DotEnvWriter;

class Settings {

    /**
     * Get settings view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user      = Auth::user();
        $timezones = Database::table("timezones")->get();
        $currencies = Database::table("currencies")->get();
        $school    = Database::table("schools")->where("id", $user->school)->first();
        $reminders = Database::table("reminders")->where("school", $user->school)->get();
        return view('settings', compact("user", "school", "reminders","currencies","timezones"));
    }

    public function getCurrencySymbol($currencyCode, $locale = 'en_US')
{
    $formatter = new \NumberFormatter($locale . '@currency=' . $currencyCode, \NumberFormatter::CURRENCY);
    return $formatter->getSymbol(\NumberFormatter::CURRENCY_SYMBOL);
}

    /**
     * Update profile on settings page
     * 
     * @return Json
     */
    public function updateprofile() {
        $account = Database::table(config('auth.table'))->where("email", input("email"))->first();
        if (!empty($account) && $account->id != Auth::user()->id) {
            exit(json_encode(responder("error", "Oops", input("email") . " already exists.")));
        }
        foreach (input()->post as $field) {
            if ($field->index == "avatar") {
                if (!empty($field->value)) {
                    $avatar = File::upload($field->value, "avatar", array(
                        "source" => "base64",
                        "extension" => "png"
                    ));
                    if ($avatar['status'] == "success") {
                        if (!empty(Auth::user()->avatar)) {
                            File::delete(Auth::user()->avatar, "avatar");
                        }
                        Database::table(config('auth.table'))->where("id", Auth::user()->id)->update(array(
                            "avatar" => $avatar['info']['name']
                        ));
                    }
                }
                continue;
            }
            if ($field->index == "csrf-token") {
                continue;
            }
            Database::table(config('auth.table'))->where("id", Auth::user()->id)->update(array(
                $field->index => escape($field->value)
            ));
        }
        return response()->json(responder("success", "Alright", "Profile successfully updated"));
    }

    /**
     * Update School on settings page
     * 
     * @return Json
     */
    public function updatecompany() {
        foreach (input()->post as $field) {
            if ($field->index == "csrf-token") {
                continue;
            }
            Database::table("schools")->where("id", Auth::user()->school)->update(array(
                $field->index => escape($field->value)
            ));
        }
        return response()->json(responder("success", "Alright", "School info successfully updated"));
    }

    /**
     * Update reminders on settings page
     * 
     * @return Json
     */
    public function updatereminders() {
        $user = Auth::user();
        if (empty(input("payment_reminders"))) {
            Database::table("schools")->where("id", $user->school)->update(array(
                "payment_reminders" => "Off"
            ));
        } else {
            Database::table("schools")->where("id", $user->school)->update(array(
                "payment_reminders" => "On"
            ));
        }
        if (empty(input("class_reminders"))) {
            Database::table("schools")->where("id", $user->school)->update(array(
                "class_reminders" => "Off"
            ));
        } else {
            Database::table("schools")->where("id", $user->school)->update(array(
                "class_reminders" => "On"
            ));
        }
        Database::table("reminders")->where("school", $user->school)->delete();
        foreach (input("message") as $index => $message) {
            $reminder = array(
                "school" => $user->school,
                "days" => input("days")[$index]->value,
                "type" => input("type")[$index]->value,
                "timing" => input("timing")[$index]->value,
                "send_via" => input("send_via")[$index]->value,
                "subject" => escape(input("subject")[$index]->value),
                "message" => escape(input("message")[$index]->value)
            );
            Database::table("reminders")->insert($reminder);
        }
        return response()->json(responder("success", "Alright", "Reminders successfully updated"));
    }

    /**
     * Update password on settings page
     * 
     * @return Json
     */
    public function updatepassword() {
        $user = Auth::user();
        if (hash_compare($user->password, Auth::password(input("current")))) {
            Database::table(config('auth.table'))->where("id", $user->id)->update(array(
                "password" => Auth::password(input("password"))
            ));
            return response()->json(responder("success", "Alright", "Password successfully updated", "reload()"));
        } else {
            return response()->json(responder("error", "Oops", "You have entered an incorrect password."));
        }
    }

    public function generateAPI() {
        $user = Auth::user();
        if (hash_compare($user->password, Auth::password(input("password")))) {

            $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
            $pass = array(); //remember to declare $pass as an array
            $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
            for ($i = 0; $i < 60; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
             }
             $newApi = implode($pass);

            Database::table(config('auth.table'))->where("id", $user->id)->update(array(
                "api_key" => $newApi));
            return response()->json(responder("success", "Alright", "API Key successfully generated", "reload()"));
        } else {
            return response()->json(responder("error", "Oops", "You have entered an incorrect password."));
        }
    }

    /**
     * Update system settings
     * 
     * @return Json
     */
    public function updatesystem() {
        $envPath = str_replace("src/Controllers", ".env", dirname(__FILE__));
        $env     = new DotEnvWriter($envPath);
        $env->castBooleans();
        $enableToggle = array(
            "ALLOW_SIGNUP",
            "SHOW_SCHOOLS_MENU"
        );
        foreach ($enableToggle as $key) {
            if (empty(input($key))) {
                $env->set($key, 'Disabled');
            }
        }
        if (empty(input("SMTP_AUTH"))) {
            $env->set("SMTP_AUTH", false);
        }
        $env->set("MAIL_SENDER", input("APP_NAME") . " <" . input("MAIL_USERNAME") . ">");
        foreach (input()->post as $field) {
            if ($field->index == "APP_LOGO") {
                if (!empty($field->value)) {
                    $upload = File::upload($field->value, "app", array(
                        "source" => "base64",
                        "extension" => "png"
                    ));
                    if ($upload['status'] == "success") {
                        File::delete(env("APP_LOGO"), "app");
                        $env->set("APP_LOGO", $upload['info']['name']);
                        $env->save();
                    }
                }
                continue;
            }
            if ($field->index == "APP_ICON") {
                if (!empty($field->value)) {
                    $upload = File::upload($field->value, "app", array(
                        "source" => "base64",
                        "extension" => "png"
                    ));
                    if ($upload['status'] == "success") {
                        File::delete(env("APP_ICON"), "app");
                        $env->set("APP_ICON", $upload['info']['name']);
                        $env->save();
                    }
                }
                continue;
            }
            if ($field->index == "csrf-token") {
                continue;
            }
            $env->set($field->index, $field->value);
            $env->save();
        }
        return response()->json(responder("success", "Alright", "System settings successfully updated", "reload()"));
    }
    
    
    /**
     * View Roles
     *
     * @return Json
     */
    public function roles()
    {
        $user = Auth::user();
        $roles = Database::table('roles')->get();
        $title = $company->name . ' - User Roles';
        return view('roles', compact('user', 'title', 'roles'));
    }

    /**
     * View Roles settings
     *
     * @return Json
     */
    public function roleSettings($id)
    {
        $user = Auth::user();
        
        $role = Database::table('roles')->where('id', $id)->first();
        $title = $role->title . ' - Role Permissions';
        return view('roleSettings', compact('user', 'title', 'role'));
    }

    /**
     * Add role
     *
     * @return Json
     */
    public function addRole()
    {
        $user = Auth::user();

        $checkExisting = Database::table('roles')->where(['title' => input('title')])
            ->get();

        if (empty($checkExisting))
        {
            if (!empty(input('inherit')))
            {
                $inheritData = Database::table('roles')->where(['id' => input('inherit') ])
                    ->first();
                $inheritData->title = input('title');
                $data = array();
                foreach ($inheritData as $key => $value)
                {
                    $data[$key] = $value;
                    unset($data["id"]);
                }
            }
            else
            {
                $data = array(
                    'title' => escape(input('title'))
                );
            }
            if (Database::table('roles')
                ->insert($data))
            {
                return response()->json(responder("success", "Role added", "Role successfully added.", "reload()"));
            }
            else
            {
                return response()
                    ->json(responder("error", "Oops!", "There was an error adding your role. Please try again.", ""));
            }
        }
        else
        {
            return response()
                ->json(responder("error", "Role exists!", "There exists a similar role. Please try again.", ""));
        }

    }

        /**
     * Delete role
     *
     * @return Json
     */
    public function deleteRole()
    {
        $users = Database::table('users')->where(['role_id' => input("roleid")])->orderby('id',false)->count("id","total")[0]->total;
        if($users < 1){
            Database::table("roles")
                ->where("id", input("roleid"))
                ->delete();

            return response()
                ->json(responder("success", "Success", "Role successfully deleted.", "redirect('" . url("Settings@roles") . "')"));
            }else{
                return response()
                    ->json(responder("error", "Users exist", "Role cannot be deleted since it has associated accounts.", ""));
            }
    }

    /**
     * Update role permission settings
     *
     * @return Json
     */
    public function updatePermissions()
    {
        $role = Database::table('roles')->where(['id' => input('roleid') ])
            ->first();
        $data = array();

        foreach (input()->post as $field)
        {
            $data[$field->index] = '1';
        }
        foreach ($role as $key => $value)
        {
            if (empty($data[$key]))
            {
                $data[$key] = '0';
            }
        }

        $data['title'] = input('title');

        unset($data["id"]);
        unset($data["roleid"]);
        unset($data["csrf-token"]);
        unset($data["company"]);
        unset($data["created_at"]);
        unset($data["updated_at"]);
        // return json_encode($data);
        if (Database::table('roles')->where(["id" => $role
            ->id])
            ->update($data))
        {
            return response()->json(responder("success", "Success", "Permissions successfully updated", "reload()"));
        }
        else
        {
            return response()
                ->json(responder("error", "Oops!", "There was an error updating your permissions. Please try again."));
        }

    }

    public function updatePayment() {
        $envPath = str_replace("src/Controllers", ".env", dirname(__FILE__));
        $env     = new DotEnvWriter($envPath);
        
        $env->set('PUBLISHABLE_KEY', input('PUBLISHABLE_KEY'));
        $env->set('SECRET_KEY', input('SECRET_KEY'));
        $env->save();
        return response()->json(responder("success", "Alright", "Payment settings successfully updated", "reload()"));
    }
}