<?php
namespace Weza\Controllers;

use Weza\Auth;
use Weza\Database;
use Weza\File;

class Responses {
    
    public function updateResponses(){
        $data = input('data');
        $data = str_replace("null", '""',$data);
        
        
        foreach(json_decode($data) as $resp){
            $query = "INSERT INTO `responses` (reference, campaign_id, msisdn, current_node, status, field0, election, presidency, issue, bbi, created_at) VALUES ('".$resp->reference."','".$resp->campaign_id."','".$resp->msisdn."','".$resp->current_node."','".$resp->status."','".$resp->field0."','".$resp->election."','".$resp->presidency."','".$resp->issue."','".$resp->bbi."','".$resp->created_on."') ON DUPLICATE KEY UPDATE campaign_id='".$resp->campaign_id."',current_node='".$resp->current_node."',status='".$resp->status."',election='".$resp->election."',presidency='".$resp->presidency."',issue='".$resp->issue."',bbi='".$resp->bbi."'";
            
            Database::table('responses')->query($query);
        }
    }
    
    public function updateResponsesNyeri(){
        $data = input('data');
        $data = str_replace("null", '""',$data);
        
        
        foreach(json_decode($data) as $resp){
            $query = "INSERT INTO `responsesNyeri` (reference, campaign_id, msisdn, current_node, status, field0, pressing_issue, governor, deputy_governor,presidency,party, mca, created_at) VALUES ('".$resp->reference."','".$resp->campaign_id."','".$resp->msisdn."','".$resp->current_node."','".$resp->status."','".$resp->field0."','".$resp->pressing_issue."','".$resp->governor."','".$resp->deputy_governor."','".$resp->presidency."','".$resp->party."','".$resp->mca."','".$resp->created_on."') ON DUPLICATE KEY UPDATE campaign_id='".$resp->campaign_id."',current_node='".$resp->current_node."',status='".$resp->status."',pressing_issue='".$resp->pressing_issue."',governor='".$resp->governor."',deputy_governor='".$resp->deputy_governor."',presidency='".$resp->presidency."',party='".$resp->party."',mca='".$resp->mca."'";
            
            Database::table('responses')->query($query);
        }
    }
    
    /**
     * Get contacts view
     * 
     * @return \Pecee\Http\Response
     */
    public function sms() {
        $user        = Auth::user();
        $responses = Database::query('select * from responses where channel="sms" order by created_at desc limit 10000');
        $title = 'SMS Responses';
        $filter = "sms";
        return view('responses', compact("user", "responses","title","filter"));
    }

    public function smsFilter() {
        $dates = self::getDates(input('daterange'));
        

        $user        = Auth::user();
        $responses = Database::query('select * from responses where channel="sms" and created_at between "'.$dates[0].' 00:00:00" and "'.$dates[1].' 23:59:59" order by id desc limit 10000');
        $title = 'SMS Responses between '.date('dS M, Y', strtotime($dates[0])).' 12:00AM and '.date('dS M, Y', strtotime($dates[1])).' 11:59 PM';
        $filter = "sms";
        return view('responses', compact("user", "responses","title","filter"));
    }
    
    public function smsBySurvey($survey) {
        
        $user        = Auth::user();
        if($survey == "kwale"){
        
        $responses = Database::query('select * from responsesNyeri where channel="sms" and campaign_id=2444 or channel="sms" and campaign_id=2444 order by created_at desc limit 10000');
        
        }elseif($survey == "taita_taveta"){
          $responses = Database::query('select * from responsesNyeri where channel="sms" and campaign_id=2090 or channel="sms" and campaign_id=2090 order by created_at desc limit 10000');
        }elseif($survey == "turkana"){
          $responses = Database::query('select * from responsesNyeri where channel="sms" and campaign_id=2443 or channel="sms" and campaign_id=2443 order by created_at desc limit 10000');
        }
        
        $title = $survey.' Responses';
        $filter = "sms";
        return view('responses', compact("user", "responses","title","filter"));
    }
    
    public function smsFilterBySurvey($survey) {
        
        
        $dates = self::getDates(input('daterange'));
        $user        = Auth::user();
        
        if($survey == "bomet"){
            $responses = Database::query('select * from responses where channel="sms" and created_at between "'.$dates[0].' 00:00:00" and "'.$dates[1].' 23:59:59" and campaign_id=2080 or channel="sms" and created_at between "'.$dates[0].' 00:00:00" and "'.$dates[1].' 23:59:59" and campaign_id=2090 order by id desc limit 10000');
        
        }elseif($survey == "london"){
            $responses = Database::query('select * from responses where channel="sms" and created_at between "'.$dates[0].' 00:00:00" and "'.$dates[1].' 23:59:59" and campaign_id=2080 or channel="sms" and created_at between "'.$dates[0].' 00:00:00" and "'.$dates[1].' 23:59:59" and campaign_id=2090 order by id desc limit 10000');
        }elseif($survey == "nyeri"){
            $responses = Database::query('select * from responses where channel="sms" and created_at between "'.$dates[0].' 00:00:00" and "'.$dates[1].' 23:59:59" and campaign_id=2470 or channel="sms" and created_at between "'.$dates[0].' 00:00:00" and "'.$dates[1].' 23:59:59" and campaign_id=2471 order by id desc limit 10000');
        }elseif($survey == "mathira"){
            $responses = Database::query('select * from responses where channel="sms" and created_at between "'.$dates[0].' 00:00:00" and "'.$dates[1].' 23:59:59" and campaign_id=2472 or channel="sms" and created_at between "'.$dates[0].' 00:00:00" and "'.$dates[1].' 23:59:59" and campaign_id=2473 order by id desc limit 10000');
        }
        
        
        
        
        $title = $survey.' SMS Responses between '.date('dS M, Y', strtotime($dates[0])).' 12:00AM and '.date('dS M, Y', strtotime($dates[1])).' 11:59 PM';
        $filter = "sms";
        return view('responses', compact("user", "responses","title","filter"));
    }

    public function ussdFilter() {
        $dates = self::getDates(input('daterange'));
        

        $user        = Auth::user();
        $responses = Database::query('select * from responses where channel="ussd" and created_at between "'.$dates[0].' 00:00:00" and "'.$dates[1].' 23:59:59" order by id desc limit 10000');
        $title = 'USSD Responses between '.date('dS M, Y', strtotime($dates[0])).' 12:00AM and '.date('dS M, Y', strtotime($dates[1])).' 11:59 PM';
        $filter = "ussd";
        return view('responses', compact("user", "responses","title","filter"));
    }
    
    public function ussd() {
        $user        = Auth::user();
        $responses = Database::query('select * from responses where channel="ussd" order by id desc limit 1000');
        $title = 'USSD Responses';
        $filter = "ussd";
        return view('responses', compact("user", "responses","title",'filter'));
    }

    function getDates($date){
        $dates = explode(' - ', $date);
        $dates[0] = date('Y-m-d', strtotime($dates[0]));
        $dates[1] = date('Y-m-d', strtotime($dates[1]));
        return $dates;
    }
    
    /**
     * Add fleet
     * 
     * @return Json
     */
    public function add() {
        $user = Auth::user();
        $data = array(
            'firstname' => escape(input('firstname')),
            'lastname' => escape(input('lastname')),
            'mobile_no' => escape(input('mobile_no')),
            'user_id' => $user->id
        );
        if(Database::table('contacts')->insert($data)){
            return response()->json(responder("success", "Contact added", "Contact successfully added.", "reload()"));
        }else{
            return response()->json(responder("error", "Oops!", "There was an error adding your contact. Please try again.", ""));
        }
    }
    
    public function import() {
        $user = Auth::user();

        if(!empty(input('contacts',null,'file')) && !input('contacts',null,'file')->hasError()){
            
            $path = $_FILES['contacts']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
                //check if file type is CSV
                if($ext == 'csv') {

                    $curlUrl='http://localhost:8000/import';  
                    $curlFile = curl_file_create(input('contacts',null,'file'));
                    $post = array('contacts'=> $curlFile );
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$curlUrl);
                    curl_setopt($ch, CURLOPT_POST,1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    $result=curl_exec($ch);

                    $contacts = json_decode($result);

                    $count = 0;
                    if (array_key_exists('firstname', $contacts[0]) && array_key_exists('lastname', $contacts[0]) && array_key_exists('mobile_no', $contacts[0])) {

                        foreach ($contacts as $contact) {
                            if( !preg_match("/^([0-1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $contact->mobile_no) ) {
                                if($contact->firstname == null){
                                    $firstname = ' ';
                                }else{
                                    $firstname = $contact->firstname;
                                }
                                if($contact->lastname == null){
                                    $lastname = ' ';
                                }else{
                                    $lastname = $contact->lastname;
                                }
                                   $data = array(
                                    'firstname' => $firstname,
                                    'lastname' => $lastname,
                                    'mobile_no' => $contact->mobile_no,
                                    'group_id' => escape(input('group_id')),
                                    'user_id' => $user->id
                                ); 
                               // return json_encode($data);
                                }
                            if(Database::table('contacts')->insert($data)){
                            $count = $count + 1;

                            }
                        }
                    return response()->json(responder('success', 'Success', number_format($count).' Contacts successfully imported','reload()')) ;
                    }else{
                        return response()->json(responder('error', 'Column title error', 'Please ensure you have the colum titles as "firstname", "lastname", "mobile_no"' ,'')) ;
                    }
                }else{
                    return response()->json(responder('error', 'File Type error', 'Please submit a .csv file','')) ;
                }

        }
        
    }
    /**
     * Delete Contact
     * 
     * @return Json
     */
    public function delete() {
        Database::table("contacts")->where("id", input("contactid"))->delete();
        return response()->json(responder("success", "Contact Deleted", "Contact successfully deleted.", "reload()"));
    }
    
    /**
     * Contact update view
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        $user        = Auth::user();
        $contact = Database::table('contacts')->where(array(
            'user_id' => $user->id,
            'id' => escape(input('contactid'))
        ))->first();
        $groups         = Database::table("groups")->where("user_id", $user->id)->get();
        return view('extras/updatecontact', compact("contact", "user","groups"));
    }
    
    /**
     * Update Contact
     * 
     * @return Json
     */
    public function update() {
        $data = array(
            'firstname' => escape(input('firstname')),
            'lastname' => escape(input('lastname')),
            'mobile_no' => escape(input('mobile_no'))
        );
        if(Database::table("contacts")->where("id", input("contactid"))->update($data)){
            return response()->json(responder("success", "Alright", "Contact successfully updated", "reload()"));
        }else{
            return response()->json(responder("error", "Oops!", "There was an error updating your contact. Please try again.", ""));
        }
    }
    
    public function receiveMO(){
        
        // self::sendSMS('254729553498','test survey response');
        // return 'ok';
        
        $mo = file_get_contents('php://input');
        error_log($mo);
        $mo = json_decode($mo);
        
        $message = $mo->message;
        $msisdn = $mo->from;
        $shortCode = $mo->to;
        // $ref = ($mo->convoId != null ? $mo->convoId : $mo->msgId);
        $ref = rand();
        
        //check if user has session
        $completeSession = Database::table('survey_sessions')->where(['msisdn'=>$msisdn,'status'=>'1'])->last();
        $session = Database::table('survey_sessions')->where(['msisdn'=>$msisdn,'status'=>'0'])->last();
        
        if(empty($completeSession) && !empty($session)){
            $fields = ["field0","field1","field2","field3","field4","field5","field6","field7","field8","field9","field10"];
            
            foreach($fields as $field){
                // return $field;
                if(empty($session->$field)){
                    
                    $current_step = $session->current_step+1;
                    
                    $questions = Database::table('survey_questions')->where(['survey'=>$session->survey])->get();
                    
                    $questions = json_decode(json_encode($questions),true);
                    
                    $question = $questions[$current_step]['question'];
                    
                    if($current_step + 1 == count($questions)){
                        $status = '1';
                    }else{
                        $status = '0';
                    }
                    
                    //store message and update step
                    $data = array(
                        'current_step'=>$current_step,
                        ''.$field.''=>$message,
                        'status'=>$status,
                        );
                        
                        // return json_encode($data);
                    
                    Database::table('survey_sessions')->where(['id'=>$session->id])->update($data);
                    // echo 'send '.$question;
                    self::sendSMS($msisdn,$shortCode, $ref, $question);
                    return json_encode(['response'=>'','code'=>'Ok']);
                    if($status == '1'){
                        //send the message here
                        // echo 'Survey Completed';
                    }
                    break;
                }
               
            }
           return json_encode(['response'=>'','code'=>'Ok']);
            
        }elseif(empty($completeSession)){
            //get survey
            $survey = Database::table('surveys')->where(['keyword'=>$message,'status'=>'1'])->first();
            
            if(!empty($survey)){
                
                //check if msisdn is invited here
                $survey_campaigns = Database::table('bulk_campaigns')->where(['survey'=>$survey->id])->get('campaign_id');
                
                $campaign_data = array(
                    'msisdn' => $msisdn,
                    'campaign_ids' => $survey_campaigns,
                    );
                    error_log(json_encode($campaign_data));
                    
                    // $message1 = Database::table('campaign_messages')->where('msisdn','LIKE','%'.$msisdn])->get();
                    $message1 = Database::query('select * from campaign_messages where msisdn LIKE "%'.substr($msisdn, -9).'"');
                $campaign = Database::table('campaigns')->where(['id'=>$message1[count($message1)-1]->campaign_id])->first();
                $survey = Database::table('surveys')->where(['id'=>$campaign->survey])->first();
                
                
                
                // return json_encode($survey;
                // if($shortCode == '23264'){
            
                //     $url = "http://164.68.104.124/reports/getInviteRA.php";
        
                // }elseif($shortCode = '40023'){
                    
                //     $url = "http://44.239.52.145/reports/getInviteRA.php";
        
                // }

                // $curl = curl_init($url);
                // curl_setopt($curl, CURLOPT_URL, $url);
                // curl_setopt($curl, CURLOPT_POST, true);
                // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                
                // $headers = array(
                //   "Content-Type: application/json",
                // );
                // curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                
                // $data = json_encode($campaign_data);
                
                // curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                
                // //for debug only!
                // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                
                // $resp = curl_exec($curl);
                // curl_close($curl);
                
                // // if($resp == 'TRUE'){
                // //     return 'Invited';
                // // }elseif($resp == 'FALSE'){
                // //     return 'Not invited';
                // // }
                
                // $resp = $resp.'';
                
                
                if($survey->status == '1' || $msisdn == "254729553498" || $msisdn == "254721855633" || $msisdn == "254722510074" || $msisdn == "254702578501" || $msisdn == "254722206154"){
              
                    
                    //create a session
                    
                    $questions = Database::table('survey_questions')->where(['survey'=>$survey->id])->get();
                    $current_step = 0;
                    $questions = json_decode(json_encode($questions),true);
                    
                    $question = $questions[$current_step]['question'];
                    
                    $session = array(
                        'survey'=>$survey->id,
                        'short_code'=>$shortCode,
                        'msisdn'=>$msisdn,
                        'field0'=>$message,
                        'current_step'=>$current_step
                        );
                        
                        
                    if(Database::table('survey_sessions')->insert($session)){
                        // echo 'send '.$question;
                        self::sendSMS($msisdn,$shortCode, $ref, $question);
                        return json_encode(['response'=>'','code'=>'Ok']);
                    };
                    return json_encode($session);
                    //send the question here
                    
                   return json_encode(['response'=>'','code'=>'Ok']);
                }
            
            } 
            return json_encode(['response'=>'','code'=>'Ok']);
           
        }else{
            return json_encode(['response'=>'','code'=>'Ok']);
        }
    }
    
private static function sendSMS($msisdn, $shortCode, $ref, $message){

        if($shortCode == '23806_SWIFT_DIAL_HTTP'){
            
            $smsData = array(
                
                    "profile_code"=> "".$shortCode."",
                        "messages"=> [
                            
                                "recipient"=> "".$msisdn."",
                                "message"=> rand()." ".$message,
                                "message_type"=>1,
                                "req_type"=> 1,
                                "external_id"=> rand()
                            
                        ],
                        "dlr_callback_url"=> "https://posthere.io/5e3d-4e84-8a26"
                    
            );

            error_log(json_encode($smsData));

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://bulkdev.swifttdial.com:2778/api/outbox/create');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,'{
                "profile_code": "'.$shortCode.'",
                  "messages": [
                      {
                          "recipient": "'.$msisdn.'",
                          "message": "'.$message.'1",
                          "message_type":1,
                          "req_type": 1,
                          "external_id": "'.rand().'"
                      }
                  ],
                  "dlr_callback_url": "https://posthere.io/5e3d-4e84-8a26"
              }');

            $headers = array();
            $headers[] = 'X-Api-Key: N2RmNTZiMDA5MDY4NTIwNUlELTA4Yjk2MGJkZDA3MDRhYjE5YWEyYTY1YWIwMWQzN2Uy';
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            error_log('send sms response '.$result);

            $sms_count = json_decode($result)[0]->sms_count;
            $charge = $sms_count * 0.6;
            $balance = Database::table('accounts')->where('id','1')->first();
            $newBalance = (float)$balance->balance - (float)$charge;
            error_log('New balance '.$newBalance);
            $updateBalance = Database::table('accounts')->where('id','1')->update(['balance'=>''.$newBalance]);

            error_log('Response: '.$result);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            return 'ok';

            // $url = 'https://sms.wezatech.co.ke/ucm_api/forwardMO.php';

            // $ch = curl_init($url);

            // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($smsData));

            // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // $result = curl_exec($ch);

            // curl_close($ch);

            // error_log('forward Result: '.$result);
            // return 'ok';

        }elseif($shortCode == '23264' || $shortCode == '23806'){
            
            $url = "http://164.68.104.124/ucm_api/index.php";
            $userId = "367";
            $orgId =  "168";
            $appId = "600141";
            $timeStamp = date('Ymdhis');//"20210915170400"
            $authKey = md5($appId.$timeStamp.'!QAZ2wsz*');

        }elseif($shortCode == '40023'){
            
            $url = "http://44.239.52.145/ucm_api/index.php";
            $userId = "531";
            $orgId = "147";
            $appId = "600145";
            $timeStamp = date('Ymdhis');//"20210915170400"
            $authKey = md5($appId.$timeStamp.'!QAZ2wsz*');

        }elseif($shortCode  == '23893' || $shortCode  == '20705'){
            $url = "http://164.68.104.124/ucm_api/index.php";
            $userId = "377";
            $orgId =  "175";
            $appId = "600142";
            $timeStamp = date('Ymdhis');//"20210915170400"
            $authKey = md5($appId.$timeStamp.'!QAZ2wsz*');
        }

        // $message =  str_replace('\\r\\n', PHP_EOL, $message);
        $message = str_replace(PHP_EOL, '%0D%0A', $message);
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $headers = array(
           "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);


        $message = str_replace(array("\n\r", "\n", "\r"), '\n', $message);
        $message = str_replace('\\n\\r', PHP_EOL, $message);
        $message = nl2br($message);
        $message = str_replace('%0D%0A','',$message);
        
        $data =array(
        "reference"=> "convoId".$ref,
        "user_id"=> $userId,
        "org_id"=> $orgId,
        "subject"=> "Survey Message",
        "src_address"=> "".$shortCode,
        "dst_address"=> $msisdn,
        "message_type"=> "1",
        "auth_key"=> $authKey,
        "message"=> "".$message.'',
        "app_id"=> $appId,
        "operation"=> "send",
        "timestamp"=> $timeStamp,
        "service_id"=> "",
        "keyword"=> ""
        );
        
        error_log('URL: '.$url.' DATA: '.json_encode($data));
        
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $resp = curl_exec($curl);
        error_log($resp);
        curl_close($curl);
    }
    
    
        public function manualMO(){
        
        $data = [["254724901387","3","40023"],
            ["254729869092","3","40023"],
            ["254711641929","3","40023"]];


        foreach($data as $dat){
                $message = $dat[1];
                $msisdn = $dat[0];
                $ref = $dat[0];
                $shortCode = $dat[2];
                
            error_log('Number '.$msisdn);
                
            $completeSession = Database::table('survey_sessions')->where(['msisdn'=>$msisdn,'status'=>'1'])->first();
            $session = Database::table('survey_sessions')->where(['msisdn'=>$msisdn,'status'=>'0'])->first();
            
            if(empty($completeSession) && empty($session)){
                error_log('Found EMPTY Number '.$msisdn);
                //get survey
                $survey = Database::table('surveys')->where(['keyword'=>$message,'status'=>'1'])->first();
                
                if(!empty($survey)){
                    
                    //check if msisdn is invited here
                    $survey_campaigns = Database::table('bulk_campaigns')->where(['survey'=>$survey->id])->get('campaign_id');
                    
                    $campaign_data = array(
                        'msisdn' => $msisdn,
                        'campaign_ids' => $survey_campaigns,
                        );
                        
                    $url = "http://44.239.52.145/reports/getInvite.php";
    
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    
                    $headers = array(
                       "Content-Type: application/json",
                    );
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                    
                    $data = json_encode($campaign_data);
                    
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                    
                    //for debug only!
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    
                    $resp = curl_exec($curl);
                    curl_close($curl);
                    
                    // if($resp == 'TRUE'){
                    //     return 'Invited';
                    // }elseif($resp == 'FALSE'){
                    //     return 'Not invited';
                    // }
                    
                    $resp = $resp.'';
                    
                    if($resp == 'YES' || $msisdn == "254729553498" || $msisdn == "254721855633" || $msisdn == "254722510074" || $msisdn == "254722206154"){
                    //if(1==1){
                        
                        //create a session
                        
                        $questions = Database::table('survey_questions')->where(['survey'=>$survey->id])->get();
                        $current_step = 0;
                        $questions = json_decode(json_encode($questions),true);
                        
                        $question = $questions[$current_step]['question'];
                        
                        $session = array(
                            'survey'=>$survey->id,
                            'msisdn'=>$msisdn,
                            'field0'=>$message,
                            'current_step'=>$current_step
                            );
                            
                        if(Database::table('survey_sessions')->insert($session)){
                            // echo 'send '.$question;
                            self::sendSMS($msisdn,$shortCode, $ref, $question);
                            // return json_encode(['response'=>'','code'=>'Ok']);
                        };
                        
                        //send the question here
                        
                    //   return json_encode(['response'=>'','code'=>'Ok']);
                    }
                
                } 
                // return json_encode(['response'=>'','code'=>'Ok']);
               
            }else{
                error_log('Number '.$msisdn.' Already responded');
                // return json_encode(['response'=>'','code'=>'Ok']);
            }
            
        
        
            }
        }
    
    
}