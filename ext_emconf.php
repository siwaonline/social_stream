<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "social_stream".
 *
 * Auto generated 11-05-2016 09:18
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'Social Stream',
  'description' => 'Crawls the data from a Facebook Page and saves to the DB, different views for show action',
  'category' => 'plugin',
  'author' => 'Philipp Parzer',
  'author_email' => 'https://forge.typo3.org/projects/extension-social_stream',
  'author_company' => 'SIWA Online GmbH',
  'state' => 'beta',
  'uploadfolder' => true,
  'createDirs' => '',
  'clearCacheOnLoad' => 0,
  'version' => '1.1.0',
  'constraints' => 
  array (
    'depends' => 
    array (
      'typo3' => '6.2.0-7.6.99',
      'scheduler' => '6.2.0-7.6.99',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
  'clearcacheonload' => false,
);

