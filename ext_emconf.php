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
	'version' => '6.2.1',
	'constraints' => array(
		'depends' => array(
			'typo3' => '12.4.0-12.4.99',
			'news' => '^11.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);
