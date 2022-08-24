<?php

namespace Weza\Controllers;



use Weza\Auth;
use Weza\Encoding;
use Weza\Database;



class SMS {

    /**

     * Get plansa view

     * 

     * @return \Pecee\Http\Response

     */

    public function get() {
        

        $user = Auth::user();
        
        $surveys = Database::table('surveys')->where(['status'=>'1'])->get();
        $title = "Create a bulk campaign";

    	

        return view('import', compact('surveys','title','user'));

    }



    public function source() {

        $user = Auth::user();

        $senders = Database::table('sender_names')->where(['user_id'=>$user->id])->get();
        $default = Database::table('sender_names')->where('type','DEFAULT')->get();
        // return json_encode($default);
        $senders  = array_merge($senders, $default);

//        $default = Database::table('sender_ids')->where(['type'=>'DEFAULT','status'=>'ACTIVE'])->get();

        $templates = Database::table('templates')->where(['user_id'=>$user->id])->get();



  //      $senders = array_merge($senders,$default);

        $source = input('source');
        if($source == 'contacts'){

            $contacts = Database::table('contacts')->where('user_id',$user->id)->limit(1000)->get();

            return view('extras/source/contacts', compact("contacts","senders","templates"));

        }elseif($source == 'groups'){

            $groups = Database::table('groups')->where('user_id',$user->id)->get();

            return view('extras/source/groups', compact("groups","senders","templates"));

        }elseif($source == 'copy'){

            return view('extras/source/copy',compact('senders',"templates"));

        }elseif($source == 'import'){

            return view('extras/source/import',compact('senders',"templates"));

        }

    }



    /**

     * Add plan

     * 

     * @return Json

     */

    public function sendFromContacts() {
        $user = Auth::user();
        $tags = array('firstname','lastname');
        $recipients = $_POST['recipients'];
        $send_time = $_POST['send_time'];
        $senderid = $_POST['senderid'];
        $campaign = $_POST['campaign'];
        $send_time = $_POST['send_time'];

        $Content = str_replace("'", "\'", input('message'));

        $smsCount = self::smsCount($Content) * count($recipients);

        if($user->sms_balance < $smsCount){
        	return response()->json(responder('error', 'Low SMS Balance', 'You have insufficient SMS balance to schedule this campaign. Please Top-up at least '.$smsCount.' units.' ,'')) ;
        }else{
        	$newBalance = $user->sms_balance - $smsCount;
        	Database::table('smsafrica_users')->where(['id'=>$user->id])->update(array('sms_balance'=>$newBalance));
        }

        $TemplateHeaders = '';

        foreach ($tags as $tag) {
            $TemplateHeaders = $TemplateHeaders.'|'.$tag;
        }
        $TemplateHeaders = ltrim($TemplateHeaders, '|');
            $messageData = array(
                'Originator'=>$senderid,
                'campaign'=>$_POST['campaign'],
                'Template'=>1,
                'Content'=>$Content,
                'TemplateHeaders' => $TemplateHeaders,
                'Dispatch_at'=>$send_time,
                'Scheduled_at'=>date('Y-m-d h:i:s'),
                'Scheduled_by'=>$user->id,
                'Approved_by'=>$user->id,
                'Queued'=>0,
                'Approved'=>1
            );

        $createbulkMessage = Database::table('bulk_message')->insert($messageData);

        $MessageId = Database::table('bulk_message')->insertId();

        foreach($recipients as $recipient){

            $recipient = Database::table('contacts')->where(['id'=>$recipient])->first();
            $data = '';
            foreach ($tags as $tag) {
                $data = $data.'|'.$recipient -> $tag;
            }
            $data = ltrim($data, '|');

            if( !preg_match("/^([0-1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $recipient->mobile_no) ) {

                $MessageData = array(
                    'msisdn'=>preg_replace('/[^0-9]/', '', $recipient->mobile_no),
                    'data'=>str_replace("'", "\'", $data),
                    'template_id'=>$MessageId,
                    'date_created'=>date('Y-m-d h:i:s')
                );
               // return json_encode($MessageData);
                if(!empty($recipient)){
                    Database::table('bulk_template_data')->insert($MessageData);
                }
            }

        }

        return response()->json(responder("success", "Campaign Queued", "Campaign successfully queued.", "reload()"));

    }



    public function sendFromGroups() {

        $user = Auth::user();
        $tags = array('firstname','lastname');

        $recipients = array();

        $groups = $_POST['recipients'];

        foreach ($groups as $group) {
            $contacts = Database::table('contacts')->where('group_id',$group)->get();
            $recipients = array_merge($recipients, $contacts);
        }

        // return json_encode($recipients);

        $send_time = $_POST['send_time'];
        $senderid = $_POST['senderid'];
        $campaign = $_POST['campaign'];
        $send_time = $_POST['send_time'];

        $Content = str_replace("'", "\'", input('message'));
        // $Content = str_replace('"', '\"', input('message'));

        $smsCount = self::smsCount($Content) * count($recipients);

        if($user->sms_balance < $smsCount){
        	return response()->json(responder('error', 'Low SMS Balance', 'You have insufficient SMS balance to schedule this campaign. Please Top-up at least '.$smsCount.' units.' ,'')) ;
        }else{
        	$newBalance = $user->sms_balance - $smsCount;
        	Database::table('smsafrica_users')->where(['id'=>$user->id])->update(array('sms_balance'=>$newBalance));
        }

        $TemplateHeaders = '';

        foreach ($tags as $tag) {
            $TemplateHeaders = $TemplateHeaders.'|'.$tag;
        }
        $TemplateHeaders = ltrim($TemplateHeaders, '|');
            $messageData = array(
                'Originator'=>$senderid,
                'campaign'=>$_POST['campaign'],
                'Template'=>1,
                'Content'=>$Content,
                'TemplateHeaders' => $TemplateHeaders,
                'Dispatch_at'=>$send_time,
                'Scheduled_at'=>date('Y-m-d h:i:s'),
                'Scheduled_by'=>$user->id,
                'Approved_by'=>$user->id,
                'Queued'=>0,
                'Approved'=>1
            );
            // return json_encode($messageData);
        $createbulkMessage = Database::table('bulk_message')->insert($messageData);

        $MessageId = Database::table('bulk_message')->insertId();

        foreach($recipients as $recipient){

            $data = '';
            foreach ($tags as $tag) {
                $data = $data.'|'.$recipient -> $tag;
            }
            $data = ltrim($data, '|');

            if( !preg_match("/^([0-1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $recipient->mobile_no) ) {
            	
            	$recipient->mobile_no = preg_replace('/[^0-9]/', '', $recipient->mobile_no);

                $MessageData = array(
                    'msisdn'=>$recipient->mobile_no,
                    'data'=>str_replace("'", "\'", $data),
                    'template_id'=>$MessageId,
                    'date_created'=>date('Y-m-d h:i:s')
                );
               // return json_encode($MessageData);

                $inQueue = Database::table('bulk_template_data')->where(['msisdn'=>$recipient->mobile_no,'template_id'=>$MessageId])->first();

                if(!empty($recipient->mobile_no) && empty($inQueue)){
                    Database::table('bulk_template_data')->insert($MessageData);
                }
            }
        }
        return response()->json(responder("success", "Campaign Queued", "Campaign successfully queued.", "reload()"));

    }



    public function sendFromCopy() {
        $user = Auth::user();
        $recipients = array();
        $recipients = $_POST['recipients'];
        $recipients = str_replace(' ', '', $recipients);
        $recipients = trim(preg_replace('/\s+/', ',', $recipients));
        $recipients = explode(',', $recipients);
        $recipients = array_unique($recipients);

        $message = str_replace("'", "\'", $_POST['message']);

        $smsCount = self::smsCount($message) * count($recipients);

        if($user->sms_balance < $smsCount){
        	return response()->json(responder('error', 'Low SMS Balance', 'You have insufficient SMS balance to schedule this campaign. Please Top-up at least '.$smsCount.' units.' ,'')) ;
        }else{
        	$newBalance = $user->sms_balance - $smsCount;
        	Database::table('smsafrica_users')->where(['id'=>$user->id])->update(array('sms_balance'=>$newBalance));
        }

        $send_time = $_POST['send_time'];
        $senderid = $_POST['senderid'];
        $campaign = $_POST['campaign'];
        $send_time = $_POST['send_time'];

        //$createCampaign = Database::table('campaigns')->insert(array('name'=>$campaign,'user_id'=>$user->id,'_from'=>$senderid));

        //$campaignId = Database::table('campaigns')->insertId();
		$messageData = array(

            'Originator'=>$senderid,
            'campaign'=>$_POST['campaign'],
            'Template'=>1,
            'Content'=>$message,
            'TemplateHeaders' => '',
            'Dispatch_at'=>$send_time,
            'Scheduled_at'=>date('Y-m-d h:i:s'),
            'Scheduled_by'=>$user->id,
            'Approved_by'=>$user->id,
            'Queued'=>0,
            'Approved'=>1
		);
    //return json_encode($messageData);
	$createbulkMessage = Database::table('bulk_message')->insert($messageData);

  	$MessageId = Database::table('bulk_message')->insertId();

		
        foreach($recipients as $recipient){
            if( !preg_match("/^([0-1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $recipient) ) {

                $data = array(
                    'msisdn'=>preg_replace('/[^0-9]/', '', $recipient),
                    'data'=>'',
                    'template_id'=>$MessageId,
                    'date_created'=>date('Y-m-d h:i:s')
                );
	           //return json_encode($data);
                if(!empty($recipient) && !empty($message)){
                    Database::table('bulk_template_data')->insert($data);
                }
            }

        }

        return response()->json(responder("success", "Campaign Queued", "Campaign successfully queued.".count($recipients), "reload()"));

    }



    public function previewFile() {

        $user = Auth::user();


        // $adapter_log_file = "/var/log/apache2/upload_log_" . date("YmdG") . ".log";

        // //initialize variables - declare 
        // $code = "999";
        // $desc = "General error";
        // $top = array();
        // $bottom = array();
        // $header = array();
        // $records = 0;
        // $filename = "";
        // $script_filename = "";
        // $file_has_header = false;
        // $upload_folder = "/var/www/research/uploads/files/";

        // if (isset($_FILES['contacts'])) {

        //     //get post data
        //     $meta = $_POST;

        //     error_log("post full: " . json_encode($_POST));
        //     $post_data = [];

        //     error_log("post_data: " . json_encode($post_data));

        //     //extract request data
        //     $post_data_array = $post_data;

        //         //get file name
        //         // $filename = $post_data['contacts'];
        //         // if (!isset($filename) || empty($filename)) {
        //             //use original file name
        //             $filename = $_FILES['contacts']['name'];
        //         // }


        //         $destination = $upload_folder . $filename;

        //         error_log("destination: " . $destination);

        //         $temp_file = $_FILES['contacts']['tmp_name'];
        //         $data_validated = true;

        //         error_log("Temp File: " . $temp_file);
        //         $columns = [];

        //         $csvData = array();
        //         if (($handle = fopen($temp_file, "r")) !== FALSE) {
        //             $index = 0;
        //             while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {

        //                 if ($index == 0) {
        //                     $num = count($row);

        //                     for ($i = 0; $i < $num; $i++) {

        //                         $row[$i] = preg_replace("/\s+/", "_", strtolower($row[$i]));
        //                     }

        //                     $mobileNumberHeaderFound = false;
        //                     foreach($row as $col){
        //                         if ($col == "mobile_no" || $col == "mobile_number" || $col == "phone" || $col == "destination") {
        //                             $mobileNumberHeaderFound = true;

        //                         }
        //                     }

        //                     if (!$mobileNumberHeaderFound) {
        //                         $code = "203";
        //                         $desc = "Cannot find mobile number column with header 'mobile_no', 'mobile_number', 'phone' or 'destination'";
        //                         $data_validated = false;
                                
        //                         $response = array(
        //                         "code" => $code,
        //                         "desc" => $desc,
        //                     );

        //                     return json_encode($response);
        //                     }

                             
        //                 }

        //                 foreach($row as $col){
        //                     array_push($columns, $col);
        //                 }
                        
        //                 error_log(json_encode($row));
        //                 for($i = 0; $i < count($row); $i++){
        //                     $csvData[] = array($columns[$i] => $row[$i]);
        //                 }
                        

        //                 $index++;
        //             }


        //             fclose($handle);
        //         }


        //         $fout = fopen($temp_file, 'w');

        //         $recipients = [];

        //         foreach ($csvData as $row) {
        //             // fputcsv($fout, $row);
        //             if(!empty($row)){
        //                 array_push($recipients, json_encode($row));
        //             }
        //         }
        //         fclose($fout);


        //         if (move_uploaded_file($_FILES['contacts']['tmp_name'], $destination)) {
        //             $response = array(
        //             "code" => '000',
        //             "desc" =>"File uploaded successfully (new filename: " . $filename,
        //             'preview'=> json_encode(array_slice($recipients, 0, 10, true)),
        //             'recipients'=>$recipients,
        //             'tags'=>['name', 'mobile_no']
        //             );
        //         }

        //         // $response = array(
        //         //     "code" => $code,
        //         //     "desc" => $desc,
        //         //     "data" => $top,
        //         //     "top" => $top,
        //         //     "bottom" => $bottom,
        //         //     "header" => $header,
        //         //     "hasHeader" => $file_has_header,
        //         //     "records" =>  $records,
        //         //     "filename" => $filename
        //         // );
        
        //         $response_body = json_encode($response);
        


        //         return json_encode($response);

        //         if ($data_validated) {
        //             //uploading
        //             if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {

        //                 $code = "000";
        //                 $desc = "File uploaded successfully (new filename: " . $filename . ") ";


        //                 $log = "Request: " . $meta['data'] . " - " . $destination . "\n";
        //                 writeToFile($adapter_log_file, $log);


        //                 error_log("file uploaded successfully: " . $destination);

        //                 //invoke the api to the core to get the file information... 
        //                 $endpoint = "http://127.0.0.1:8787/api/file/validate"; // default
        //                 $page_size = 3;
        //                 if (isset($file_validate_url)) {
        //                     $endpoint = $file_validate_url;
        //                 }
        //                 if (isset($file_validate_page_size)) {
        //                     $page_size = $file_validate_page_size;
        //                 }
        //                 error_log("file validate endpoint: $endpoint, page size: $page_size ");

        //                 $request = array();
        //                 $request['fileName'] = $filename;
        //                 $request['pageSize'] = $page_size;

        //                 $request_body = json_encode($request);

        //                 error_log("file validate endpoint: $endpoint, request body: $request_body ");

        //                 //open connection
        //                 $curl = curl_init();

        //                 curl_setopt_array($curl, array(
        //                     CURLOPT_URL => $endpoint,
        //                     CURLOPT_RETURNTRANSFER => true,
        //                     CURLOPT_ENCODING => "",
        //                     CURLOPT_MAXREDIRS => 10,
        //                     CURLOPT_TIMEOUT => 0,
        //                     CURLOPT_FOLLOWLOCATION => true,
        //                     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //                     CURLOPT_CUSTOMREQUEST => "POST",
        //                     CURLOPT_POSTFIELDS => $request_body,
        //                     CURLOPT_HTTPHEADER => array(
        //                         "Content-Type: application/json"
        //                     ),
        //                 ));

        //                 //actual call to the api
        //                 $response_body = curl_exec($curl);

        //                 error_log("file validate endpoint: $endpoint, response body: $response_body ");

        //                 if ($response_body === FALSE) {
        //                     error_log("file validate endpoint: $endpoint, response body: $response_body " . curl_error($curl));
        //                     $code = "204";
        //                     $desc = "Error validating the file";
        //                 } else {
        //                     //happy path - success
        //                     $response = json_decode($response_body, true);
        //                     $validation_code = $response['code'];
        //                     $validation_desc = $response['desc'];

        //                     error_log("file validate endpoint: $endpoint, code: $validation_code, desc: $validation_desc");

        //                     if ($validation_code == 'OK') {
        //                         //we are good... 
        //                         $records = $response['fileInfo']['recordCount'];
        //                         $header = $response['fileInfo']['header'];
        //                         $top = $response['fileInfo']['top'];
        //                         $bottom = $response['fileInfo']['bottom'];
        //                         $file_has_header = $response['fileInfo']['hasHeader'];

        //                         error_log("file validate endpoint: $endpoint, records: $records, header: " . json_encode($header) . ", top: " . json_encode($top) . ", bottom: " . json_encode($bottom) . ", has_header: $file_has_header| data to sent back");

        //                         if ($records == 0) {
        //                             //we are not okay... 
        //                             $code = "205";
        //                             $desc = "File $filename is empty. $records records. ";
        //                         }
        //                     } else {
        //                         //we are not okay... 
        //                         $code = "205";
        //                         $desc = "Error validating the file: $validation_desc";
        //                     }
        //                 }

        //                 curl_close($curl);
        //             } else {
        //                 $log = "Request: " . $meta['data'] . " - " . $destination . "\n";
        //                 writeToFile($adapter_log_file, $log);
        //                 $code = "203";
        //                 $desc = "Uploading the file failed.";
        //             }
        //         }
            
        // } else {
        //     $log = "Request: no file specified.\n";
        //     writeToFile($adapter_log_file, $log);
        //     $code = "001";
        //     $desc = "File upload failed. No file Specified. Please try again";
        // }

        // /**
        // * ---- old data ----
        // * data - top N records 
        // * records - total number of records in file 
        // * filename - uploaded file name 
        // * ---- old data ----
        // * top - top N records 
        // * bottom - bottom N records 
        // * header - the head data.... 
        // */

        // $response_body = "";
       
        // $response = array(
        //     "code" => $code,
        //     "desc" => $desc,
        //     "data" => $top,
        //     "top" => $top,
        //     "bottom" => $bottom,
        //     "header" => $header,
        //     "hasHeader" => $file_has_header,
        //     "records" =>  $records,
        //     "filename" => $filename
        // );

        // $response_body = json_encode($response);

        // error_log("file validate endpoint: $endpoint, respons: $$response_body");
    


        // return $response_body;

        


        if(!empty(input('contacts',null,'file')) && !input('contacts',null,'file')->hasError()){

            

            $path = $_FILES['contacts']['name'];

            $ext = pathinfo($path, PATHINFO_EXTENSION);

                //check if file type is CSV

                if($ext == 'csv') {


                    



                    $curlUrl='https://smsafrica.ml:446/public/import';  

                    $curlFile = curl_file_create(input('contacts',null,'file'));

                    $post = array('contacts'=> $curlFile );

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL,$curlUrl);

                    curl_setopt($ch, CURLOPT_POST,1);

                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

                    $result=curl_exec($ch);



                    $recipients = json_decode($result);

                    if (array_key_exists('mobile_no', $recipients[0])) {

                        $titles = [];

                        for ($i=0; $i < sizeof($recipients); $i++) {

                            $recipient = $recipients[$i];

                            foreach($recipient as $key=>$value){

                                array_push($titles, $key);

                            }

                        }

                        $tags = array_unique($titles);



                        $data = array('tags'=>$tags, 'recipients'=>$recipients);



                        $preview = $output = array_slice($recipients, 0, 10, true);

                        $response = array(

                            'preview'=> $preview,

                            'recipients'=>json_encode($recipients),

                            'tags'=>$tags

                        );

                        return json_encode($response);

                    }else{

                        return response()->json(responder('error', 'Column title error', 'Please rename the recipients phone number to "mobile_no"' ,'')) ;

                    }

                }else{

                    return response()->json(responder('error', 'File Type Error', 'Please submit a .csv file','')) ;

                }



        }

        

    }



    public function sendFromFile() {
        
        $user = Auth::user();
        $recipients = json_decode($_POST['recipients']);

        // $recipients = array_unique($recipients);

        $send_time = $_POST['send_time'];
        $senderid = $_POST['senderid'];
        $survey = $_POST['survey'];
        $campaign = $_POST['campaign'];
        $send_time = $_POST['send_time'];
        $titles = [];

        for ($i=0; $i < sizeof($recipients); $i++) {
            $recipient = $recipients[$i];
             foreach($recipient as $key=>$value){
                 array_push($titles, $key);
              }
           }

        $message = str_replace("'", "\'", input('message'));

        $smsCount = self::smsCount($message) * count($recipients);

        // if($user->sms_balance < $smsCount){
        // 	return response()->json(responder('error', 'Low SMS Balance', 'You have insufficient SMS balance to schedule this campaign. Please Top-up at least '.$smsCount.' units.' ,'')) ;
        // }else{
        // 	$newBalance = $user->sms_balance - $smsCount;
        // 	Database::table('smsafrica_users')->where(['id'=>$user->id])->update(array('sms_balance'=>$newBalance));
        // }

        $tags = array_unique($titles);
        $TemplateHeaders = '';

        foreach ($tags as $tag) {
            $TemplateHeaders = $TemplateHeaders.'|'.$tag;
        }
        $TemplateHeaders = ltrim($TemplateHeaders, '|');
        $messageData = array(
            'sender'=>$senderid,
            'name'=>$_POST['campaign'],
            'message'=>$message,
            'survey'=>$survey,
            'Dispatch_at'=>$send_time,
            'Scheduled_at'=>date('Y-m-d h:i:s'),
            'Scheduled_by'=>$user->id,
            'Queued'=>0,
            'Approved'=>0
        );
        // error_log(json_encode($messageData));
        $createbulkMessage = Database::table('campaigns')->insert($messageData);

        $MessageId = Database::table('bulk_message')->insertId();

        foreach($recipients as $recipient){
            $recipient->message = '';
            // return 'TAGS : '.json_encode($tags);
            foreach ($tags as $tag) {

                if( strpos( $message, '{'.$tag.'}' ) !== false) {
                    $theMessage = str_replace('{'.$tag.'}',$recipient -> $tag,$message);
                }
                
            }
            if( !preg_match("/^([0-1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $recipient->mobile_no) ) {
                $msisdn = preg_replace('/[^0-9]/', '', $recipient->mobile_no);

                $MessageData = array(
                    'msisdn'=>'254'.substr($msisdn, -9),
                    'sender'=>$senderid,
                    'campaign_id'=>$MessageId,
                    'message'=>$theMessage,
                    'status'=>'-1',
                    'approved'=>'0'
                );
            //     return $message;
            //    return json_encode($MessageData);
                if(!empty($recipient)){
                    Database::table('campaign_messages')->insert($MessageData);
                    error_log(json_encode($MessageData));
                }
            }

          }
        //   return $MessageId;
            return redirect(url('').'campaign/'.$MessageId);
          return response()->json(responder("success", "Campaign Queued", "Campaign successfully queued.", "redirect(".url('')."/campaign/".$MessageId.")"));



        }

    public function quick(){

        $user = Auth::user();
        $recipient = input('mobile_no');
        $senderid = $_POST['senderid'];

        $message = str_replace("'", "\'", input('message'));

        $smsCount = self::smsCount($message);

        if($user->sms_balance < $smsCount){
        	return response()->json(responder('error', 'Low SMS Balance', 'You have insufficient SMS balance to schedule this campaign. Please Top-up at least '.$smsCount.' units.' ,'')) ;
        }else{
        	$newBalance = $user->sms_balance - $smsCount;
        	Database::table('smsafrica_users')->where(['id'=>$user->id])->update(array('sms_balance'=>$newBalance));
        }

        $TemplateHeaders = 'firstname|lastname';

        $messageData = array(
                'Originator'=>$senderid,
                'campaign'=>'Quick'.date('Ymdhis'),
                'Template'=>1,
                'Content'=>$message,
                'TemplateHeaders' => $TemplateHeaders,
                'Dispatch_at'=>date('Y-m-d h:i:s'),
                'Scheduled_at'=>date('Y-m-d h:i:s'),
                'Scheduled_by'=>$user->id,
                'Approved_by'=>$user->id,
                'Queued'=>0,
                'Approved'=>1
            );
        // return json_encode($messageData);
        $createbulkMessage = Database::table('bulk_message')->insert($messageData);

        $MessageId = Database::table('bulk_message')->insertId();

        $data = ' | ';

        if( !preg_match("/^([0-1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $recipient) ) {

            $MessageData = array(
                'msisdn'=>$recipient,
                'data'=>$data,
                'template_id'=>$MessageId,
                'date_created'=>date('Y-m-d h:i:s')
            );
           // return json_encode($MessageData);
            if(!empty($recipient)){
                if(Database::table('bulk_template_data')->insert($MessageData)){
                    return response()->json(responder("success", "Message Queued", "Message successfully queued.", "reload()"));
                }else{
                    return response()->json(responder("error", "Queuing Error", "There was an error queuing your message. Please try again", "")); 
                }
            }
        }else{
            return response()->json(responder("error", "Invalid recipient", "Please check your recipient and try again", "")); 
        }
    }

    public function loadSMS(){
        return 'ok';
      
        $smss = Database::table('campaign_messages')->where(['status'=>'-1','Approved'=>'1'])->orderBy('id',true)->limit('200')->get();
        foreach($smss as $sms){

            error_log('Sending : '.json_encode($sms));

            $send = self::sendSMS($sms->sender, $sms->msisdn, $sms->message, $sms->id);
            error_log($send);

            
        }

    }

    public function receiveDLR(){

        $dlr = file_get_contents('php://input');
        error_log('Delivery Receipt '.$dlr);

        $id = json_decode($dlr)->external_id;
        $status = json_decode($dlr)->reason;


        $update = Database::table('campaign_messages')->where(['id'=>$id])->update(['status'=>$status]);


    }

    private static function sendSMSTest($from, $to, $message, $id){

        $ch = curl_init();
        $url = 'https://bulkdev.swifttdial.com:2778/api/outbox/create';
        // $url = 'http://research.levak.co.ke/receiveDLR';
        // $url = 'https://sms.wezatech.co.ke/ucm_api/forwardMO.php';

        $message = str_replace(array("\n\r", "\n", "\r", "\\t"), '\n', $message);

        curl_setopt($ch, CURLOPT_URL, ''.$url.'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,'{
            "external_id" : "'.$id.'",
            "status" : "delivered",
            "reason" : "DeliveredToTerminal"
          }');

        $headers = array();
        $headers[] = 'X-Api-Key: N2RmNTZiMDA5MDY4NTIwNUlELTA4Yjk2MGJkZDA3MDRhYjE5YWEyYTY1YWIwMWQzN2Uy';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        error_log('Response: '.$result);
        if (curl_errno($ch)) {
            error_log('Error:' . curl_error($ch));
        }
        curl_close($ch);


        $response = array(
                "external_id"=> "".$id,
                "recipient"=> "254780011971",
                "sms_count"=>1
        );
        return json_encode($response);
    }

    private static function sendSMS($from, $to, $message, $id){
       
        // $smsData = array(
                
        //     "profile_code"=> "".$from."",
        //         "messages"=> [
                    
        //                 "recipient"=> "".$to."",
        //                 "message"=> stripslashes(trim($message)),
        //                 "message_type"=>1,
        //                 "req_type"=> 1,
        //                 "external_id"=> $id
                    
        //         ],
        //         "dlr_callback_url"=> ""
            
        // );

        // error_log(json_encode($smsData));

        $ch = curl_init();
        $url = 'https://bulkdev.swifttdial.com:2778/api/outbox/create';
        // $url = 'http://research.levak.co.ke/testTPS.php';
        // $url = 'https://sms.wezatech.co.ke/ucm_api/forwardMO.php';

        $message = str_replace(array("\n\r", "\n", "\r"), '\n', $message);

        curl_setopt($ch, CURLOPT_URL, ''.$url.'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,'{
            "profile_code": "'.$from.'",
            "messages": [
                {
                    "recipient": "'.$to.'",
                    "message": "'.trim($message).'",
                    "message_type":1,
                    "req_type": 1,
                    "external_id": "'.$id.'"
                }
            ],
            "dlr_callback_url": "http://research.levak.co.ke/receiveDLR"
        }');

        $headers = array();
        $headers[] = 'X-Api-Key: N2RmNTZiMDA5MDY4NTIwNUlELTA4Yjk2MGJkZDA3MDRhYjE5YWEyYTY1YWIwMWQzN2Uy';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        error_log('Response: '.$result);
        if (curl_errno($ch)) {
            error_log('Error:' . curl_error($ch));
        }
        curl_close($ch);
        

        $external_id = json_decode($result)[0]->external_id;
            $sms_count = json_decode($result)[0]->sms_count;
            $charge = $sms_count * 0.6;
            
            if($external_id == $id){
                $update = Database::table('campaign_messages')->where(['id'=>$id])->update(['status'=>'SENT','sms_count'=>''.$sms_count, 'charge'=>$charge]);
                $balance = Database::table('accounts')->where('id','1')->first();
                $newBalance = (float)$balance->balance - (float)$charge;
                $newBalance = Database::table('accounts')->where('id',1)->update(['balance'=>$newBalance]);
            }

    }

    public function test(){

        $recipient = input('mobile_no');
        $senderid = "SMSAfrica";

        $message = str_replace("'", "\'", input('message'));

        $TemplateHeaders = 'firstname|lastname';

        $messageData = array(
                'Originator'=>$senderid,
                'campaign'=>'Quick'.date('Ymdhis'),
                'Template'=>1,
                'Content'=>$message,
                'TemplateHeaders' => $TemplateHeaders,
                'Dispatch_at'=>date('Y-m-d h:i:s'),
                'Scheduled_at'=>date('Y-m-d h:i:s'),
                'Scheduled_by'=>446,
                'Approved_by'=>446,
                'Queued'=>0,
                'Approved'=>1
            );
        // return json_encode($messageData);
        $createbulkMessage = Database::table('bulk_message')->insert($messageData);

        $MessageId = Database::table('bulk_message')->insertId();

        $data = ' | ';

        if( !preg_match("/^([0-1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $recipient) ) {

            $MessageData = array(
                'msisdn'=>$recipient,
                'data'=>$data,
                'template_id'=>$MessageId,
                'date_created'=>date('Y-m-d h:i:s')
            );
           // return json_encode($MessageData);
            if(!empty($recipient)){
                if(Database::table('bulk_template_data')->insert($MessageData)){
                    return response()->json(responder("success", "Message Queued", "Message successfully queued.", "reload()"));
                }else{
                    return response()->json(responder("error", "Queuing Error", "There was an error queuing your message. Please try again", "")); 
                }
            }
        }else{
            return response()->json(responder("error", "Invalid recipient", "Please check your recipient and try again", "")); 
        }
    }

    public function campaigns() {

        $user = Auth::user();

        $campaigns = Database::table('campaigns')->orderBy('id', false)->get();

        foreach ($campaigns as $campaign) {
           $campaign->messages = Database::query('select count(*) as count from campaign_messages where campaign_id="'.$campaign->id.'"');

        }

        

        return view('campaigns', compact('user','campaigns'));

    }



    public function outbox() {

        $user = Auth::user();
        $senders = Database::table('sender_names')->where(['user_id'=>$user->id,'status'=>'ACTIVE'])->get();
        $default = Database::table('sender_names')->where(['type'=>'DEFAULT','status'=>'ACTIVE'])->get();
        $senders = array_merge($senders,$default);

        $messages = Database::table('dbqueue_bulk')->orderby('RecordID',false)->limit(1000)->get();
        foreach($messages as $message){
            $message->campaign = Database::table('bulk_message')->where(['RecordID'=>$message->MessageId])->first();
            $message->sender = Database::table('sender_names')->where(['header'=>$message->Originator])->first();
            $cdr = Database::table('dbcdr_bulk')->where('MessageId',$message->RecordID)->first();
            if(!empty($cdr)){
                $delivery = Database::table('delivery_bulk')->where('Correlator',$cdr->Correlator)->first();
                $message->status = $delivery->Status;
            }else{
                $message->status = 'Pending';
            }

        }
        // return json_encode($messages);

        return view('outbox', compact('user','messages','senders'));

    }



    public function campaignDetails($id) {

        $user = Auth::user();

        $campaign = Database::table('campaigns')->where('id',$id)->first();
        $messages = Database::table('campaign_messages')->where('campaign_id', $id)->get();
        
        $messageCount = Database::query('select count(*) as count from campaign_messages where campaign_id="'.$id.'"');
        $deliveredCount = Database::query('select count(*) as count from campaign_messages where status="DeliveredToTerminal" and campaign_id = '.$id.'');
        $failedCount = Database::query('select count(*) as count from campaign_messages where status="NOT-SENT" and campaign_id="'.$id.'"');
        $newCount = Database::query('select count(*) as count from campaign_messages where status = "SENT" and campaign_id="'.$id.'"');
        $messageCount = Database::query('select count(*) as count from campaign_messages where campaign_id="'.$id.'"');
        
        
        $sampleMessages = Database::table('campaign_messages')->where('campaign_id', $id)->limit(10)->get();
        
        $maker = Database::table('users')->where(['id'=>$campaign->Scheduled_by])->first();
        $checker = Database::table('users')->where(['id'=>$campaign->Approved_by])->first();
        

        return view('campaign-details', compact('user','campaign','messages', 'messageCount','sampleMessages','deliveredCount','failedCount','newCount','maker','checker'));

    }

    
    private static function smsCount($message)
    {
        $msg_count = 0;
        $message_length = strlen($message);
        if ( ($message_length > 160) && ($message_length>0) && (160>0) ) {
            $nosms = round( ($message_length/160) , 0);
            $nosms1 = $nosms*160;
            if( ($message_length-$nosms1) > 0 ){
                $msg_count =  $nosms + 1;
            }else{
                $msg_count = $nosms;
            }

        }else{
            $msg_count = 1;
        }
        return $msg_count;
    }

    public function deleteCampaign() {

        $campaign = Database::table("campaigns")->where("id", input("id"))->delete();

        $messages = Database::table('campaign_messages')->where('campaign_id',input("id"))->delete();

        return response()->json(responder("success", "Campaign ".input('campaignid')." Deleted", "Campaign successfully deleted.", "reload()"));

    }
    public function approveCampaign() {
        $user = Auth::user();

        $campaign = Database::table("campaigns")->where("id", input("id"))->update(['Approved'=>'1','Approved_by'=>$user->id]);

        // $messages = Database::table('dbqueue_bulk')->where('MessageId',input("campaignid"))->delete();
        // $messages1 = Database::table('bulk_template_data')->where('template_id',input("campaignid"))->delete();

        return response()->json(responder("success", "Campaign ".input('id')." Approved", "Campaign successfully approved.", "reload()"));

    }



    public function deleteMessage() {

        $messages = Database::table('dbqueue_bulk')->where('RecordID',input("messageid"))->delete();

        return response()->json(responder("success", "Message Deleted", "Message successfully deleted.", "reload()"));

    }



    public function saveLog(){

        $log = input('log').PHP_EOL;

        file_put_contents('./logs/sysem_log-'.date("Y-m-d").'.log', $log.PHP_EOL, FILE_APPEND);

    }



    public function topup(){

        $user = Auth::user();

        return view('topup',compact('user'));

    }



    public function checkout(){

        $curlUrl='https://smsafrica.ml:446/public/checkout';  

        

        $post = array('phone_number'=> input('phone'),'amount' => input('amount'),'account_no'=>input('account_number') );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$curlUrl);

        curl_setopt($ch, CURLOPT_POST,1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        $result=curl_exec($ch);



        if(!empty($result)){

            return response()->json(responder("success", "Request Sent!", "An M-PESA checkout request has been sent to your phone.", "reload()"));

        }else{

            return response()->json(responder("error", "Checkout Failed!", "Your checkout request failed. Please try again.", "reload()"));

        }



        $user = Auth::user();

        return view('topup',compact('user'));

    }



    public function buy(){

        $user = Auth::user();

        return view('buy',compact('user'));

    }



    public function buySMS(){

        $user = Auth::user();

        $sms = input('sms');

        $cost = input('cost');

        if($cost > $user->cash_balance){

            return response()->json(responder("error", "Insufficient Balance", "You have insufficient cash balance to buy ".number_format($sms)." SMS units. Please top up and try again.", ""));

        }else{

            $newCashBalance = $user->cash_balance - $cost;

            $newSmsBalance = $user->sms_balance + $sms;



            if(Database::table('smsafrica_users')->where('id',$user->id)->update(['cash_balance'=>$newCashBalance,'sms_balance'=>$newSmsBalance])){

                return response()->json(responder("success", "Alright", "You have successfully purchased SMS Units","reload()"));

            }else{

                return response()->json(responder("error", "Oops", "There was an error purchasing your SMS units. Please try again.", ""));

            }

        }

    }

}
