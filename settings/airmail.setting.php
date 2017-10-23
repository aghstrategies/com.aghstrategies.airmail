<?php

return array(
  'airmail_secretcode' => array(
    'group_name' => 'Airmail Preferences',
    'group' => 'airmail',
    'name' => 'airmail_secretcode',
    'type' => 'String',
    'default' => NULL,
  ),
  'airmail_open_click_processor' => array(
    'group_name' => 'Airmail Preferences',
    'group' => 'airmail',
    'name' => 'airmail_open_click_processor',
    'type' => 'String',
    'default' => 'CiviMail',
  ),
  'airmail_external_smtp_service' => array(
    'group_name' => 'Airmail Preferences',
    'group' => 'airmail',
    'name' => 'airmail_external_smtp_service',
    'type' => 'String',
    'default' => 'ses',
  ),
);
