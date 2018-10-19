<?php

use CRM_Airmail_Utils as E;

class CRM_Airmail_Backend_Ses implements CRM_Airmail_Backend {

  public function processInput($input) {
    return json_decode($input);
  }

  public function validateMessages($events) {
    // No validation performed at this point.
    return TRUE;
  }

  /**
   * Process Events from Amazon SNS on behalf of Amazon SES
   *
   * @param  object $events json decoded object sent from SES
   */
  public function processMessages($events) {
    switch ($events->Type) {
      case 'SubscriptionConfirmation':
        // If the message is to confirm subscription to SNS
        if (!empty($events->SubscribeURL)) {
          // Go to the subscribe URL to confirm end point
          // TODO: parse the xml and save the info to civi just in case
          $snsResponse == file_get_contents($events->SubscribeURL);
        }
        break;

      case 'Notification':
        // If the message is a notification of a mailing event
        $responseMessage = json_decode($events->Message);
        if (empty($responseMessage->mail->source)) {
          // TODO: do we care in this situation?
        }

        $mailingJobInfo = E::parseSourceString($responseMessage->mail->source);

        $params = [
          'job_id' => $mailingJobInfo['job_id'],
          'event_queue_id' => $mailingJobInfo['event_queue_id'],
          'hash' => $mailingJobInfo['hash'],
        ];

        switch ($responseMessage->notificationType) {
          case 'Bounce':
            $params['body'] = "Bounce Description: {$responseMessage->bounce->bounceType} {$responseMessage->bounce->bounceSubType}";
            CRM_Airmail_EventAction::bounce($params);
            break;

          case 'Complaint':
            // TODO opt out contact entirely
            CRM_Airmail_EventAction::spamreport($params);
            break;
        }
        break;
    }
  }

  /**
   * Called by hook_civicrm_alterMailParams
   *
   * @param array $params
   *   The mailing params
   * @param string $context
   *   The mailing context.
   */
  public function alterMailParams(&$params, $context) {}

}
