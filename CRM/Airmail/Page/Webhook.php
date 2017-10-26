<?php
use CRM_Airmail_ExtensionUtil as E;

class CRM_Airmail_Page_Webhook extends CRM_Core_Page {

  public function run() {
    // get information sent from Amazon SNS
    $events = json_decode(file_get_contents('php://input'));
    $settings = CRM_Airmail_Utils::getSettings();

    // NOTE if you want to log the contents of the post un comment this line
    //  CRM_Core_Error::debug_log_message('sns' . print_r($events, TRUE), FALSE, 'AirmailWebhook');

    // TODO make sure its coming from url with secret code by uncommenting section below
    if (!$events || !is_array($events)) {
      CRM_Core_Error::debug_log_message('events failing', FALSE, 'AirmailWebhook');
    }
    if (!empty($settings['secretcode']) && $settings['secretcode'] != CRM_Utils_Array::value('secretcode', $_REQUEST)) {
      CRM_Core_Error::debug_log_message('secret code failing', FALSE, 'AirmailWebhook');
    }
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
      $mailingJobInfo = array();
      if (!empty($responseMessage->mail->source)) {
        $mailingJobInfo = CRM_Airmail_Utils::parseSourceString($responseMessage->mail->source);
      }
      if (!empty($responseMessage->notificationType) && !empty($mailingJobInfo)) {
        switch ($responseMessage->notificationType) {
          case 'Bounce':
            $body = "Bounce Description: {$responseMessage->bounce->bounceType} {$responseMessage->bounce->bounceSubType}";
            CRM_Airmail_BAO_Airmail::bounce($mailingJobInfo['job_id'], $mailingJobInfo['event_queue_id'], $mailingJobInfo['hash'], $body);
            break;

          case 'Delivery':
            break;

          case 'Complaint':
            CRM_Airmail_BAO_Airmail::spamreport($mailingJobInfo['job_id'], $mailingJobInfo['event_queue_id'], $mailingJobInfo['hash']);
            break;

          default:
            # code...
            break;
        }
      }
    }
    CRM_Utils_System::civiExit();
  }

}
