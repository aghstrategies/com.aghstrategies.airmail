<?php
use CRM_Airmail_ExtensionUtil as E;

class CRM_Airmail_Page_Webhook_Ses extends CRM_Airmail_Page_Webhook {

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(E::ts('Webhook_Ses'));

    // Example: Assign a variable for use in a template
    $this->assign('currentTime', date('Y-m-d H:i:s'));

    parent::run();
  }

}
