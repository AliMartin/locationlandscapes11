<?php

$settings['update_free_access'] = FALSE;
$settings['file_private_path'] = DRUPAL_ROOT . '/private-files';
$settings['file_temp_path'] = DRUPAL_ROOT . '/tmp';



$settings['trusted_host_patterns'] = [
  '^locationlandscapes\.com$',
  '^.+\.locationlandscapes\.com$',
  '^locationlandscapes\.hyperluminal\.co\.uk$',
  '^.+\.locationlandscapes\.hyperluminal\.co\.uk$',
  '^locationlandscapes10\.ddev\.site$',
  '^.+\.locationlandscapes10\.ddev\.site$',
];

$settings['config_sync_directory'] = DRUPAL_ROOT . '/sync';

