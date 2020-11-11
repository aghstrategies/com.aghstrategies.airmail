<?php

return [
  'airmail_secretcode' => [
    'group_name' => 'Airmail Preferences',
    'group' => 'airmail',
    'name' => 'airmail_secretcode',
    'type' => 'String',
    'default' => NULL,
  ],
  'airmail_external_smtp_service' => [
    'group_name' => 'Airmail Preferences',
    'group' => 'airmail',
    'name' => 'airmail_external_smtp_service',
    'type' => 'String',
    'default' => 'ses',
  ],
  'airmail_ee_wrapunsubscribe' => [
    'group_name' => 'Airmail Preferences',
    'group' => 'airmail',
    'name' => 'airmail_ee_wrapunsubscribe',
    'type' => 'Boolean',
    'default' => FALSE,
  ],
];
