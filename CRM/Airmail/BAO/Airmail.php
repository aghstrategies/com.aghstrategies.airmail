<?php

class CRM_Airmail_BAO_Airmail {
  /**
   * Record Bounce Event
   * @param  int $job   Job id
   * @param  int $queue event queue id
   * @param  string $hash  hash
   * @param  string $body  description
   */
  public static function bounce($job, $queue, $hash, $body = 'unknown') {
    $params = array(
      'job_id' => $job,
      'event_queue_id' => $queue,
      'hash' => $hash,
      'body' => $body,
    );
    try {
      $bounceEvent = civicrm_api3('Mailing', 'event_bounce', $params);
    }
    catch (CiviCRM_API3_Exception $e) {
      CRM_Core_Error::debug_log_message("Airmail webhook (bounce)\n" . $e->getMessage());
    }
  }

  public static function unsubscribe($job_id, $event_queue_id, $hash) {
    try {
      $result = civicrm_api3('MailingEventUnsubscribe', 'create', array(
        'job_id' => $job_id,
        'hash' => $hash,
        'event_queue_id' => $event_queue_id,
      ));
    }
    catch (CiviCRM_API3_Exception $e) {
      CRM_Core_Error::debug_log_message("Airmail webhook" . $e->getMessage());
    }
  }

  public static function spamreport($job_id, $event_queue_id, $hash) {
    // TODO: This needs to be replaced with something else like in
    // https://github.com/cividesk/com.cividesk.email.sparkpost/blob/master/CRM/Sparkpost/Page/callback.php#L95
    // which isn't ideal but will do the trick.
    // CRM_Mailing_Event_BAO_SpamReport::report($event->event_queue_id);
    self::unsubscribe($job_id, $event_queue_id, $hash);
  }

}
