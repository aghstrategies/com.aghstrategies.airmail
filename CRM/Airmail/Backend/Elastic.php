<?php

use CRM_Airmail_Utils as E;

class CRM_Airmail_Backend_Elastic implements CRM_Airmail_Backend {

  public function processInput($input) {
    // ElasticEmail sends parameters in $_GET
    return $_GET;
  }

  public function validateMessages($event) {
    return !empty($event['transaction']);
  }

  public function processMessages($event) {
    $status = $event['status'];
    //Parse postback url to get all required mailing information.
    $mailingJobInfo = E::parseSourceString($event['postback']);
    //Map Elastic email bounce types and resoan with CiviCRM.
    $bounce_details = self::getBounceTypeMessages($event);
    $params = [
      'job_id' => $mailingJobInfo['job_id'],
      'event_queue_id' => $mailingJobInfo['event_queue_id'],
      'hash' => $mailingJobInfo['hash'],
      'bounce_type_id' => $bounce_details['bounce_type_id'],
      'bounce_reason' => $bounce_details['bounce_reason'],
    ];
    switch ($status) {
      // When you want to receive notifications for bounced emails.
      case 'Bounce / Error':
      case 'Bounce':
      case 'Error':
        // Add bounce report in CiviCRM.
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
    // Add custom headers for ElasticEmail
    // This is required so we will get postback URL in Elastic email response,
    // and can then identify the mailing item, source contact, etc.
    if ($context != 'messageTemplate') {
      if (!array_key_exists('headers', $params)) $params['headers'] = array();
      $params['headers']['X-ElasticEmail-Postback'] = $params['Return-Path'];
    }
  }

 /**
  *
  * Function for getting bounce type and message.
  */
  public function getBounceTypeMessages($event) {

    // Start from the ElasticEmail bounce codes
    // see https://help.elasticemail.com/en/articles/2300650-what-are-the-bounce-error-categories-and-filters
    $elastic_mail_messages = array(
      'Unknown' => 'Unknown Error',
      'Throttled' => 'The recipient server did not accept the email within 48 hours',
      'GreyListed' => 'The email was not delivered because the recipient server has determined that this email has not been seen in the configuration provide',
      'Timeout' => 'The email was not delivered because a timeout occurred',
      'NoMailbox' => 'The email address does not appear to exist',
      'NotDelivered' => 'The recipient has a blocked status for either hard bouncing, being unsubscribed or complained',
      'DNSProblem' => 'The domain part of the address does not exist',
      'AccountProblem' => 'There is something wrong with the mailbox of the recipient, eg. the mailbox is full and cannot accept more emails',
      'SPFProblem' => 'The email was not delivered because there was an issue validating the SPF record for the domain of this email',
      'ContentFilter' => 'Unknown Error',
      'Spam' => 'The email was rejected because it matched a profile the internet community has labeled as Spam',
      'Blacklisted' => 'Email is black listed',
      'ConnectionTerminated' => 'The status of the email is not known for sure because the recipient server terminated the connection without returning a message code or status',
      'ConnectionProblem' => 'The email was not delivered because of a connection problem',
      'AbuseReport' => 'Unknown Error',
    );

    // Map Elastic Email bounce categories to generic bounce types
    $elastic_categories = array(
      'Away' => array('Unknown', 'Throttled', 'Timeout', 'SPFProblem', 'ConnectionProblem'),
      'Relay' => array('Greylisted', 'NotDelivered', 'ConnectionTerminated'),
      'Invalid' => array('NoMailbox', 'Suppressed'),
      'Spam' => array('ContentFilter', 'Spam', 'Blacklisted'),
      'Abuse' => array('AbuseReport'),
      'DNS' => array('DNSProblem'),
      'Inactive' => array('AccountProblem'),
    );

    // Map generic bounce types to CiviCRM bounce_type IDs
    $civicrm_bounces = array(
      'Away' => 2,    // soft, retry 30 times
      'Relay' => 9,   // soft, retry 3 times
      'Invalid' => 6, // hard, retry 1 time
      'Spam' => 10,   // hard, retry 1 time
      'Abuse' => 10,  // hard, retry 1 time
      'DNS' => 3,
      'Inactive' => 5,
    );

    // Determine bounce type id from ElasticEmail category
    foreach ($elastic_categories as $value => $categories) {
      if (in_array($event['category'], $categories)){
         $bounce_type_id = $civicrm_bounces[$value];
      }
    }

    // Add a description for the cause of the bounce (map from Elastic Email bounce category).
    if (array_key_exists($event['category'], $elastic_mail_messages)) {
      $bounce_reason = $elastic_mail_messages[$event['category']];
    } else {
      $bounce_reason = 'Unknown';
    }

    // return CiviCRM bounce code and description.
    return ['bounce_type_id' =>  $bounce_type_id, 'bounce_reason' => $bounce_reason];
  }
}
