<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Social Stream',
	'description' => 'Crawls the data from a Social Media Page and saves it as news record',
	'category' => 'plugin',
	'author' => 'Philipp Parzer',
	'author_email' => 'https://forge.typo3.org/projects/extension-social_stream',
	'author_company' => 'SIWA Online GmbH',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '1',
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '2.0.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '7.6.0-8.7.99',
			'news' => '^5.3.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);