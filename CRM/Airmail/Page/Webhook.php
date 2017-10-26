<?php
use CRM_Airmail_ExtensionUtil as E;

class CRM_Airmail_Page_Webhook extends CRM_Core_Page {

  public function run() {
    // get information sent from Amazon SNS
    $events = json_decode(file_get_contents('php://input'));
    $settings = CRM_Airmail_Utils::getSettings();

    // NOTE if you want to log the contents of the post un comment this line
    //  CRM_Core_Error::debug_log_message('sns' . print_r($events, TRUE), FALSE, 'AirmailWebhook');

    if (!$events || (!empty($settings['secretcode']) && $settings['secretcode'] != CRM_Utils_Array::value('secretcode', $_REQUEST))) {
      // We should expect a json encoded array of events from the external smtp service
      // if that's not what we get, we're done here
      // or if the secret code doesn't match
      CRM_Utils_System::setHttpHeader("Status", "404 Not Found");
      CRM_Utils_System::civiExit();
    }

    // IF SES
    if ($settings['external_smtp_service'] == 'SES') {
      CRM_Airmail_BAO_Ses::processEvents($events);
    }
    CRM_Utils_System::civiExit();
  }

}
