<?php

use CRM_Airmail_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Airmail_Form_DeleteMyEmail extends CRM_Core_Form {
  /**
   * Prevent people double-submitting the form (e.g. by double-clicking).
   * https://lab.civicrm.org/dev/core/-/issues/1773
   *
   * @var bool
   */
  public $submitOnce = TRUE;

  protected $_hasBeenDone = FALSE;

  public function preProcess() {
    error_log("preProcess running");
  }
  public function buildQuickForm() {
    error_log("buildQuickForm running");

    // @todo check hash.
    $contactID = (int) ($_GET['cid'] ?? 0);
    $checksum = $_GET['cs'] ?? '';
    $emailID = (int) ($_GET['eid'] ?? 0);

    $authorized = FALSE;
    $error = NULL;

    if (!empty($_GET['completed']) || $this->_hasBeenDone) {
      // Completed, or being processed.
    }
    else {
      if (($contactID > 0) && $checksum && ($emailID > 0)) {
        // Check checksum
        if (CRM_Contact_BAO_Contact_Utils::validChecksum($contactID, $checksum)) {
          // Check email exists.
          $email = \Civi\Api4\Email::get(FALSE)
            ->setCheckPermissions(FALSE)
            ->addWhere('id', '=', $emailID)
            ->addWhere('contact_id', '=', $contactID)
            ->execute()->first();
          if (!$email) {
            $error = E::ts('Your email has already been deleted.');
          }
          else {
            $this->assign('email', preg_replace('/^(.).*?@(..).*(\.[^.]+)$/', '$1***@$2***$3', $email['email']));
            $authorized = TRUE;
          }
        }
        else {
          $error = E::ts('This link has expired.');
        }
      }
      else {
        $error = E::ts('Invalid link.');
      }
    }

    $this->assign('authorized', $authorized);
    if ($error) {
      CRM_Core_Session::setStatus($error, 'Error', 'crm-error no-popup');
    }

    // add form elements
    $this->add( 'hidden', 'cs', $checksum);
    $this->add( 'hidden', 'emailID', $emailID);
    $this->add( 'hidden', 'contactID', $contactID);

    $this->addRadio(
      'optoutoptions', // field name
      'Opt-out option', // field title/label
      [
        'optout' => E::ts('Opt-out of all bulk mailings'),
        'delete' => E::ts('Opt-out of all bulk mailings and delete my email'),
      ],
      [],
      '<br><br>', // separator
      TRUE // is required
    );

    $this->setDefaults(['optoutoptions' => 'optout']);

    $this->addButtons(array(
      array(
        'type'      => 'submit',
        'name'      => E::ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();

    // Always set optout
    $contactID = $values['contactID'];
    if (!$contactID) {
      // Should never happen.
      throw new \InvalidArgumentException("Missing Contact ID");
    }
    \Civi\Api4\Contact::update(FALSE)
      ->setCheckPermissions(FALSE)
      ->addWhere('id', '=', $contactID)
      ->addValue('is_opt_out', TRUE)
      ->setLimit(1)
      ->execute();
    $message = E::ts("You have opted-out of all bulk emails");

    if ($values['optoutoptions'] === 'delete') {
      // Delete their email.
      \Civi\Api4\Email::delete(FALSE)
        ->setCheckPermissions(FALSE)
        ->addWhere('id', '=', $emailID)
        ->addWhere('contact_id', '=', $contactID)
        ->execute();
      $message = E::ts("You have opted-out of all bulk emails and your email has been deleted.");
    }

    CRM_Core_Session::setStatus($message, 'Success', 'crm-success no-popup');
    $this->_hasBeenDone = TRUE;
    error_log("done all processing");
    parent::postProcess();
    error_log("calling redirect");

    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/deletemyemail', ['completed' => 1]));
  }

  public function getColorOptions() {
    $options = array(
      '' => E::ts('- select -'),
      '#f00' => E::ts('Red'),
      '#0f0' => E::ts('Green'),
      '#00f' => E::ts('Blue'),
      '#f0f' => E::ts('Purple'),
    );
    foreach (array('1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e') as $f) {
      $options["#{$f}{$f}{$f}"] = E::ts('Grey (%1)', array(1 => $f));
    }
    return $options;
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
