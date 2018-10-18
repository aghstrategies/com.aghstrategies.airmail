<?php

use CRM_Airmail_Utils as E;

class CRM_Airmail_EventAction {
  /**
   * Record Bounce Event
   * @param array $params
   *   Params to pass to the CiviCRM API
   */
  public static function bounce($params) {
    if (empty($params['body'])) {
      $params['body'] = E::ts('unknown');
    }
    try {
      $bounceEvent = civicrm_api3('Mailing', 'event_bounce', $params);
    }
    catch (CiviCRM_API3_Exception $e) {
      CRM_Core_Error::debug_log_message("Airmail API error (bounce)\n" . $e->getMessage());
    }
  }

  public static function unsubscribe($params) {
    try {
      $result = civicrm_api3('MailingEventUnsubscribe', 'create', $params);
    }
    catch (CiviCRM_API3_Exception $e) {
      CRM_Core_Error::debug_log_message("Airmail API error (unsubscribe)" . $e->getMessage());
    }
  }

  public static function spamreport($params) {
    // TODO: This needs to be replaced with something else like in
    // https://github.com/cividesk/com.cividesk.email.sparkpost/blob/master/CRM/Sparkpost/Page/callback.php#L95
    // which isn't ideal but will do the trick.
    // CRM_Mailing_Event_BAO_SpamReport::report($params['event_queue_id']);
    self::unsubscribe($params);
  }

}
