<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once 'vendor/autoload.php';

use Weza\Application;
use Weza\Database;
use Weza\Auth;
use Weza\Mail;
use Weza\Sms;

$app = new Application();
 date_default_timezone_set('Africa/nairobi');
$today = date("Y-m-d h:i:s");

/**
 * Update missed classes
 * 
 */
error_log('Cron Job Running');
$campaigns = Database::query("select * from campaigns where Approved='1' and Dispatch_at <= '".$today."' and Queued ='0'");
if ( count($campaigns) > 0 ) {
	foreach ($campaigns as $campaign) {
		//send the pending messages
		echo $campaign->id;
		Database::table('campaigns')->where(['id'=>$campaign->id])->update(['Queued'=>'1']);
		Database::table('campaign_messages')->where(['campaign_id'=>$campaign->id])->update(['Approved'=>'1']);
	}
}

