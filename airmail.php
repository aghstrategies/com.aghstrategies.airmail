<?php

require_once 'airmail.civix.php';
use CRM_Airmail_Utils as E;


/**
 * Implements hook_civicrm_alterMailParams().
 */
function airmail_civicrm_alterMailParams(&$params, $context) {
  $backend = E::getBackend();
  if (!$backend || !in_array('CRM_Airmail_Backend', class_implements($backend))) {
    return;
  }

  $backend->alterMailParams($params, $context);
}

/**
 * hook_civicrm_navigationMenu
 *
 * add "Airmail Configuration" to the Mailings menu
 */
function airmail_civicrm_navigationMenu(&$menu) {

  $adder = new CRM_Airmail_NavAdd($menu);

  $attributes = array(
    'label' => ts('Airmail Configuration'),
    'name' => 'Airmail Configuration',
    'url' => 'civicrm/airmail/settings',
    'permission' => 'access CiviMail,administer CiviCRM',
    'operator' => 'AND',
    'separator' => 1,
    'active' => 1,
  );
  $adder->addItem($attributes, array('Mailings'));
  $menu = $adder->getMenu();
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function airmail_civicrm_config(&$config) {
  _airmail_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function airmail_civicrm_install() {
  _airmail_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function airmail_civicrm_enable() {
  _airmail_civix_civicrm_enable();
}
