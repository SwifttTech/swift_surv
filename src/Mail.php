<?php
namespace Weza;
use Mailjet\Resources;

class Mailer {}

class Mail {

    /**
     * Send an email
     * 
     * @param   string          $view_name
     * @param   string|array    $to
     * @param   string          $subject
     * @param   mixed           $from
     * @param   array           $attachments
     * @param   \PDO            $pdo
     * 
     * @return  void
     */
        public static function send($to, $subject, array $view_data, $view_name = "basic", $from = null, array $attachments = []) {
        $mailer = container(Mailer::class);
        $view_data = array_merge($view_data, array(
                                "appurl" => env('APP_URL'),
                                "applogo" => env('APP_URL')."/uploads/app/".env('APP_LOGO'),
                                "copyright" => "&copy; ".date("Y")." ".env("APP_NAME")." | All Rights Reserved."
                            ));

        if (is_null($from)) {
            $from = config('mail.from');
        }
        list($from_name, $from_email) = array_merge(explode(' <', str_replace('>', '', $from)), [null]);
        $from_email = is_null($from_email) ? $from_name : $from_email;
        $mailer->setFrom($from_email, $from_name);

        if (is_string($to)) {
            $to = array('To' => array($to));
        }
        if (!isset($to['Cc'])) {
            $to['Cc'] = array();
        }
        if (!isset($to['Bcc'])) {
            $to['Bcc'] = array();
        }
        foreach ($to['To'] as $recipient) {
            list($to_name, $to_email) = array_merge(explode(' <', str_replace('>', '', $recipient)), [null]);
            $to_email = is_null($to_email) ? $to_name : $to_email;
            $mailer->addAddress($to_email, $to_name);
        }
        foreach ($to['Cc'] as $recipient) {
            list($cc_name, $cc_email) = array_merge(explode(' <', str_replace('>', '', $recipient)), [null]);
            $cc_email = is_null($cc_email) ? $cc_name : $cc_email;
            $mailer->addCC($cc_email, $cc_name);
        }
        foreach ($to['Bcc'] as $recipient) {
            list($bcc_name, $bcc_email) = array_merge(explode(' <', str_replace('>', '', $recipient)), [null]);
            $bcc_email = is_null($bcc_email) ? $bcc_name : $bcc_email;
            $mailer->addBCC($bcc_email, $bcc_name);
        }

        foreach ($attachments as $key => $value) {
            if (is_numeric($key)) {
                $mailer->addAttachment($value); 
            } else {
                $mailer->addAttachment($value, $key);
            }
        }
            
        $mailer->isHTML(true);
        $mailer->Subject = $subject;
        $mailer->Body    = view("emails.html.{$view_name}", $view_data);

        return $mailer->send(); 

    }

    public static function sendNow($to, $subject, array $view_data, $view_name = "basic", $from = null, array $attachments = []) {
        $mailer = container(Mailer::class);
        $view_data = array_merge($view_data, array(
                                "appurl" => env('APP_URL'),
                                "applogo" => env('APP_URL')."/uploads/app/".env('APP_LOGO'),
                                "copyright" => "&copy; ".date("Y")." ".env("APP_NAME")." | All Rights Reserved."
                            ));
        $message = $view_data['message'];
        $title = $view_data['title'];
        $buttonLink = $view_data['buttonLink'];
        $buttonText = $view_data['buttonText'];
        $subtitle = $view_data['subtitle'];
        $name = $view_data['name'];
        $appurl = env('APP_URL');
        $copyright = "&copy; ".date("Y")." ".env("APP_NAME")." | All Rights Reserved.";
        $applogo = env('APP_URL')."/uploads/app/".env('APP_LOGO');


          $send = new \Mailjet\Client('e7e20935837a31216d2ecb360e349911','4626881f6bbd5e01af6b48617fbfe676',true,['version' => 'v3.1']);
              $body = [
                'Messages' => [
                  [
                    'From' => [
                      'Email' => "info@smsafrica.tech",
                      'Name' => "SMS Africa"
                    ],
                    'To' => [
                      [
                        'Email' => $to,
                        'Name' => $name
                      ]
                    ],
                    'Subject' => $subject,
                    'TextPart' => "My first Mailjet email",
                    'HTMLPart' => view('emails/html/withbutton', compact('appurl','applogo','copyright','message','title','subtitle','buttonLink','buttonText')),
                    'CustomID' => "AppGettingStartedTest"
                  ]
                ]
              ];
              $resp = $send->post(Resources::$Email, ['body' => $body]);
              // $resp->success() && var_dump($resp->getData());

    }
}