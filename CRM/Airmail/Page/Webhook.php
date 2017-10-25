<?php
use CRM_Airmail_ExtensionUtil as E;

class CRM_Airmail_Page_Webhook extends CRM_Core_Page {

  public function run() {
    // get information sent from Amazon SNS
    $events = json_decode(file_get_contents('php://input'));

    // NOTE if you want to log the contents of the post un comment this line
    //  CRM_Core_Error::debug_log_message('sns' . print_r($events, TRUE), FALSE, 'AirmailWebhook');

    // TODO make sure its coming from url with secret code by uncommenting section below
    // if (!$events || !is_array($events)
    //   || (!empty($settings['secretcode']) && $settings['secretcode'] != CRM_Utils_Array::value('secretcode', $_REQUEST))) {
    //   // Ses sends a json encoded array of events
    //   // if that's not what we get, we're done here
    //   // or if the secret code doesn't match
    //   CRM_Utils_System::setHttpHeader("Status", "404 Not Found");
    //   CRM_Utils_System::civiExit();
    // }

    //  If the message is to confirm subscription to SNS
    if ($events->Type == 'SubscriptionConfirmation' && !empty($events->SubscribeURL)) {
      // Go to the subscribe URL to confirm end point
      // TODO parse the xml and save the info to civi just in case
      $snsResponse == file_get_contents($events->SubscribeURL);
    }

    // If the message is a notification of a mailing event
    if ($events->Type == 'Notification' && !empty($events->Message)) {
      $responseMessage = json_decode($events->Message);
      $mailingJobInfo = NULL;
      if (!empty($responseMessage->mail->source)) {
        $mailingJobInfo = self::parseSource($responseMessage->mail->source);
      }
      if (!empty($responseMessage->notificationType) && !empty($mailingJobInfo)) {
        switch ($responseMessage->notificationType) {
          case 'Bounce':
            self::bounce($mailingJobInfo);
            break;

          default:
            # code...
            break;
        }
      }
    }
    CRM_Utils_System::civiExit();
  }

  public static function bounce($details) {
    try {
      civicrm_api3('Mailing', 'event_bounce', array(
        'job_id' => $event->job_id,
        'event_queue_id' => $event->event_queue_id,
        'hash' => $event->hash,
        'body' => $event->reason,
      ));
    }
    catch (CiviCRM_API3_Exception $e) {
      CRM_Core_Error::debug_log_message("Airmail webhook (bounce)\n" . $e->getMessage());
    }
  }

  public static function parseSource($string) {
    // $string ex: b.179.46.731d881bbb3f9aad@sestest.garrison.aghstrategies.net
    // Based off of https://github.com/civicrm/civicrm-core/blob/master/CRM/Utils/Mail/EmailProcessor.php
    $regex = '/^' . preg_quote($dao->localpart) . '(b|c|e|o|r|u)' . $twoDigitString . '([0-9a-f]{16})@' . preg_quote($dao->domain) . '$/';
    CRM_Core_Error::debug_log_message($regex, FALSE, 'AirmailWebhook');
  }

}
