<?php

use CRM_Airmail_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Airmail_Form_Airmail_Settings extends CRM_Core_Form {
  public function buildQuickForm() {
    $settings = CRM_Airmail_Utils::getSettings();

    // compile what the endpoint url will look like
    $q = empty($settings['secretcode']) ? 'reset=1' : "reset=1&secretcode={$settings['secretcode']}";
    $url = CRM_Utils_System::url('civicrm/sendgrid/webhook', $q, TRUE, NULL, FALSE, TRUE);

    // Add form Elements
    $attr = NULL;
    $secretCode = $this->add('text', 'secretcode', ts('Secret Code'), $attr, TRUE);
    $secretCode->setSize(40);
    $clickProcessor = $this->add('select', 'open_click_processor', ts('Open / Click Processing'), NULL, TRUE);
    $clickProcessor->loadArray(array('CiviMail' => ts('CiviMail'), 'Never' => ts('Do No Track'), 'SendGrid' => ts('SendGrid')));
    $smptpService = $this->add('select', 'external_smtp_service', ts('External SMTP Service'), NULL, TRUE);
    $smptpService->loadArray(array('SES' => ts('Amazon SES')));
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Save Configuration'),
        'isDefault' => TRUE,
      ),
    ));

    $this->setDefaults($settings);
    $this->assign('url', $url);

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess() {
    // save settings to database
    $vars = $this->getSubmitValues();
    $settings = CRM_Airmail_Utils::getSettings();
    foreach ($vars as $k => $v) {
      if (array_key_exists($k, $settings)) {
        $settings[$k] = $v;
      }
    }
    CRM_Airmail_Utils::saveSettings($settings);

    // $values = $this->exportValues();
    // $options = $this->getColorOptions();
    // CRM_Core_Session::setStatus(E::ts('You picked color "%1"', array(
    //   1 => $options[$values['favorite_color']],
    // )));
    parent::postProcess();

    // CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/airmail/settings', 'reset=1'));
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
