<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'Socialstream.' . $_EXTKEY,
	'Pi1',
	'Social Stream'
);

if (TYPO3_MODE === 'BE') {

	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'Socialstream.' . $_EXTKEY,
		'web',	 // Make module a submodule of 'web'
		'be1',	// Submodule key
		'',						// Position
		array(
			'Page' => 'listbe, showbe,  new, create, edit, update, delete, message',
			'Facebook' => 'token, create',
			'Xing' => 'token, create',
			'LinkedIn' => 'token, create',
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.' . (\TYPO3\CMS\Core\Utility\GeneralUtility::compat_version('7.0') ? 'png' : 'gif'),
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be1.xlf',
		)
	);
}

$pluginSignature = str_replace('_', '', $_EXTKEY) . '_pi1';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Flexform.xml');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Social Stream');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_socialstream_domain_model_page', 'EXT:social_stream/Resources/Private/Language/locallang_csh_tx_socialstream_domain_model_page.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_socialstream_domain_model_page');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_socialstream_domain_model_post', 'EXT:social_stream/Resources/Private/Language/locallang_csh_tx_socialstream_domain_model_post.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_socialstream_domain_model_post');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_socialstream_domain_model_gallery', 'EXT:social_stream/Resources/Private/Language/locallang_csh_tx_socialstream_domain_model_gallery.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_socialstream_domain_model_gallery');


