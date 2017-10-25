<?php
use CRM_Airmail_ExtensionUtil as E;

class CRM_Airmail_Page_Webhook extends CRM_Core_Page {

  public function run() {
    // get information sent from Amazon SNS
    $events = json_decode(file_get_contents('php://input'));
    // NOTE if you want to log the contents of the post un comment this line
    //  CRM_Core_Error::debug_log_message('sns' . print_r($events, TRUE), FALSE, 'AirmailWebhook');
    if ($events->Type == 'SubscriptionConfirmation' && !empty($events->SubscribeURL)) {
      // Go to the subscribe URL to confirm end point
      // TODO parse the xml and save the info to civi just in case
      $snsResponse == file_get_contents($events->SubscribeURL);
    }
    // Example: Assign a variable for use in a template
    $this->assign('currentTime', date('Y-m-d H:i:s'));
    CRM_Utils_System::civiExit();
  }

}
