<?php

use CRM_Airmail_Utils as E;

class CRM_Airmail_Backend_Elastic implements CRM_Airmail_Backend {

  public function processInput($input) {
    return json_decode($input);
  }

  public function validateMessages($events) {
    return TRUE;
  }

  public function processMessages($events) {
    #TODO process elastic email events.
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
