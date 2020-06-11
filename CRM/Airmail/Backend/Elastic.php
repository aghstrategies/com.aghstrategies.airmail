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
    $status = $event['status'];
    $mailingJobInfo = E::parseSourceString($event['from']);
    $params = [
      'job_id' => $mailingJobInfo['job_id'],
      'event_queue_id' => $mailingJobInfo['event_queue_id'],
      'hash' => $mailingJobInfo['hash'],
    ];
    switch ($status) {
      // When you want to receive notifications for bounced emails.
      case 'Bounce / Error':
      case 'Bounce':
      case 'Error':
        CRM_Core_Error::debug_log_message("Bounce Error:\n" . print_r($event, TRUE));
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
