<?php

class CRM_Airmail_Utils {
  /**
   * Get Settings if Set
   * @return array key is setting name value is value of setting
   */
  public static function getSettings() {
    $settings = Civi::cache()->get('airmailSettings');
    if (empty($settings)) {
      $settings = array(
        'secretcode' => NULL,
        'open_click_processor' => NULL,
        'external_smtp_service' => NULL,
      );
      foreach ($settings as $setting => $val) {
        try {
          $settings[$setting] = civicrm_api3('Setting', 'getvalue', array(
            'name' => "airmail_$setting",
            'group' => 'Airmail Preferences',
          ));
        }
        catch (CiviCRM_API3_Exception $e) {
          $error = $e->getMessage();
          CRM_Core_Error::debug_log_message(ts('API Error: %1', array(
            1 => $error,
            'domain' => 'com.aghstrategies.airmail',
          )));
        }
      }
      Civi::cache()->set('airmailSettings', $settings);
    }
    return $settings;
  }

  /**
   * Save airmail settings
   * @param  array $settings settings to save
   */
  public static function saveSettings($settings) {
    $existingSettings = Civi::cache()->get('airmailSettings');
    $settingsToSave = array();
    foreach ($settings as $k => $v) {
      $existingSettings[$k] = $v;
      $settingsToSave["airmail_$k"] = $v;
    }
    try {
      $settingsSaved = civicrm_api3('Setting', 'create', $settingsToSave);
    }
    catch (CiviCRM_API3_Exception $e) {
      $error = $e->getMessage();
      CRM_Core_Error::debug_log_message(ts('API Error: %1', array(
        1 => $error,
        'domain' => 'com.aghstrategies.airmail',
      )));
    }
  }

  /**
   * breakes down source string
   * @param  string $string string of job id hash and queue ex: b.179.46.731d881bbb3f9aad@ex.com
   * @return array   including job id, event queue id and hash
   */
  public static function parseSourceString($string) {
    $dao = new CRM_Core_DAO_MailSettings();
    $dao->domain_id = CRM_Core_Config::domainID();
    $dao->find();
    while ($dao->fetch()) {
      // 0 = activities; 1 = bounce in this case we are just looking for bounce
      if ($dao->is_default == 1) {

        // empty array to use for preg match
        $matches = array();

        // Get Verp separtor setting
        $config = CRM_Core_Config::singleton();
        $verpSeperator = preg_quote($config->verpSeparator);

        $twoDigitStringMin = $verpSeperator . '(\d+)' . $verpSeperator . '(\d+)';
        $twoDigitString = $twoDigitStringMin . $verpSeperator;
        // $string ex: b.179.46.731d881bbb3f9aad@sestest.garrison.aghstrategies.net
        // Based off of https://github.com/civicrm/civicrm-core/blob/master/CRM/Utils/Mail/EmailProcessor.php
        $regex = '/^' . preg_quote($dao->localpart) . '(b|c|e|o|r|u)' . $twoDigitString . '([0-9a-f]{16})@' . preg_quote($dao->domain) . '$/';
        if (preg_match($regex, $string, $matches)) {
          list($match, $action, $job, $queue, $hash) = $matches;
          $bounceEvent = array(
            'job_id' => $job,
            'event_queue_id' => $queue,
            'hash' => $hash,
          );
          return $bounceEvent;
        }
      }
    }
  }

}
