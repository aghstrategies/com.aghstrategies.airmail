<?php
use CRM_Airmail_ExtensionUtil as E;

class CRM_Airmail_Page_Webhook extends CRM_Core_Page {

  public function run() {

    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    $events = json_decode(file_get_contents('php://input'));
    //CRM_Core_Error::debug_log_message(print_r(getallheaders(), TRUE), FALSE, 'AirmailWebhook');
    CRM_Core_Error::debug_log_message(print_r($events->Type, TRUE), FALSE, 'AirmailWebhook');
    //CRM_Core_Error::debug_log_message(print_r($_POST, TRUE), FALSE, 'AirmailWebhook');
    if ($events->Type == 'SubscriptionConfirmation' && !empty($events->SubscribeURL)) {
     $snsResponse == file_get_contents($events->SubscribeURL);
     CRM_Core_Error::debug_log_message('sns' . print_r($snsResponse, TRUE), FALSE, 'AirmailWebhook');
    }
    // Example: Assign a variable for use in a template
    $this->assign('currentTime', date('Y-m-d H:i:s'));
    CRM_Utils_System::civiExit();
  }

}
