<?php
/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$link = mysqli_connect("localhost", "root", "!QAZ2wsy", "ucm");

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

$tracking_id = $_GET['id'];
//exit($end);
// Attempt select query execution
$sql = 'select id, msisdn, current_node, status, field0, field3,field4,field5,field6,field7,field8,field9 from tbl_campaign_sessions where org_id=28 and id >= '.$tracking_id.'';

if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){
        $data = mysqli_fetch_array($result);

        $ch = curl_init();
        $curlConfig = array(
        // CURLOPT_URL            => "http://91.194.91.79:9293/smsReport",
        CURLOPT_URL            => "https://posthere.io/48f3-4447-a4de",
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS     => array(
        'data' => $data
    )
);
curl_setopt_array($ch, $curlConfig);
$curlResult = curl_exec($ch);
if (curl_errno($ch)) {
echo    $error_msg = curl_error($ch);
}


curl_close($ch);
    } else{
        echo "No records matching your query were found.";
    }
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}

// Close connection
mysqli_close($link);
?>