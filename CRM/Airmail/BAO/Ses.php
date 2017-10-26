<?php

class CRM_Airmail_BAO_Ses {
  /**
   * Process Events from Amazon SNS on behalf of Amazon SES
   * @param  object $events json decoded object sent from SES
   */
  public static function processEvents($events) {
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
      if (!empty($responseMessage->notificationType) && !empty($mailingJobInfo) && $mailingJobInfo['job_id']) {
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

  }

}
