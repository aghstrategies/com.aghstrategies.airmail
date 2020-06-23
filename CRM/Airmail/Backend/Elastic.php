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
    //Parse postback url to get all required mailing information.
    $mailingJobInfo = E::parseSourceString($event['postback']);
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
        // Add bounce report in CiviCRM.
        CRM_Airmail_EventAction::bounce($params);
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
    // Add custom headers for ElasticEmail
    // This is required so we will get postback in Elastic email response.
    if ($context != 'messageTemplate') {
      if (!array_key_exists('headers', $params)) $params['headers'] = array();
      $params['headers']['X-ElasticEmail-Postback'] = $params['Return-Path'];
    }
  }
}
