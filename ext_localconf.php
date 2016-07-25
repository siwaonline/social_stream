<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Socialstream\\SocialStream\\Controller\\GetSocialCommandController';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Socialstream.' . $_EXTKEY,
	'Pi1',
	array(
		'Page' => 'list, show, message, showSinglePost, showSingleEvent',
		'Facebook' => '',
		'Xing' => '',
		'LinkedIn' => '',
		
	),
	// non-cacheable actions
	array(
		'Page' => '',
		'Facebook' => '',
		'Xing' => '',
		'LinkedIn' => '',
	)
);
