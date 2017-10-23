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

}
