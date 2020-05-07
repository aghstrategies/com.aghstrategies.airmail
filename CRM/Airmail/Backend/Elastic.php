<?php

use CRM_Airmail_Utils as E;

class CRM_Airmail_Backend_Elastic implements CRM_Airmail_Backend {

  public function processInput($input) {
    return $input;
  }

  public function validateMessages($event) {
    return !empty($event['transaction']);
  }

  public function processMessages($event) {
    CRM_Core_Error::debug_log_message("Airmail Elastic Request:\n" . print_r($event, TRUE));
    $status = $event['status'];
    $mailingJobInfo = E::parseSourceString($event['from']);
    $params = [
      'job_id' => $mailingJobInfo['job_id'],
      'event_queue_id' => $mailingJobInfo['event_queue_id'],
      'hash' => $mailingJobInfo['hash'],
    ];
    switch ($status) {
      // When you want to receive notifications for opened emails.
      case 'Opened':
        CRM_Airmail_EventAction::open($params);
        break;

      // When you want to receive notifications for clicked emails.
      case 'Clicked':
        if (!empty($event['target'])) {
          $params['url'] = $event['target'];
        }
        CRM_Airmail_EventAction::click($params);
        break;

      // When you want to receive notifications about users unsubscribing
      // from your email.
      case 'Unsubscribed':
        CRM_Airmail_EventAction::unsubscribe($params);
        break;

      // When you want to receive notifications for all types of complaints.
      case 'Complaints':
        // TODO Opt out contact entirely.
        CRM_Airmail_EventAction::spamreport($params);
        break;

      // When you want to receive notifications for bounced emails.
      // TODO Confirm if we need all three below.
      case 'Bounce / Error':
      case 'Bounce':
      case 'Error':
        $params['body'] = 'Bounce / Error Category: ' . $event['category'];
        CRM_Airmail_EventAction::bounce($params);
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
  public function alterMailParams(&$params, $context) {

  }

}
