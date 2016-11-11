<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Social Stream');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_socialstream_domain_model_channel', 'EXT:social_stream/Resources/Private/Language/locallang_csh_tx_socialstream_domain_model_channel.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_socialstream_domain_model_channel');

$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Imaging\\IconRegistry');
$iconRegistry->registerIcon('extensions-' . $_EXTKEY . '-facebook', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', array("source" => 'EXT:social_stream/Resources/Public/Icons/socialstream_domain_model_channel_facebook.svg',));
$iconRegistry->registerIcon('extensions-' . $_EXTKEY . '-instagram', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', array("source" => 'EXT:social_stream/Resources/Public/Icons/socialstream_domain_model_channel_instagram.svg',));
$iconRegistry->registerIcon('extensions-' . $_EXTKEY . '-youtube', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', array("source" => 'EXT:social_stream/Resources/Public/Icons/socialstream_domain_model_channel_youtube.svg',));
$iconRegistry->registerIcon('extensions-' . $_EXTKEY . '-twitter', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', array("source" => 'EXT:social_stream/Resources/Public/Icons/socialstream_domain_model_channel_twitter.svg',));
$iconRegistry->registerIcon('extensions-' . $_EXTKEY . '-xing', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', array("source" => 'EXT:social_stream/Resources/Public/Icons/socialstream_domain_model_channel_xing.svg',));
$iconRegistry->registerIcon('extensions-' . $_EXTKEY . '-linkedin', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', array("source" => 'EXT:social_stream/Resources/Public/Icons/socialstream_domain_model_channel_linkedin.svg',));
$iconRegistry->registerIcon('extensions-' . $_EXTKEY . '-soundcloud', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', array("source" => 'EXT:social_stream/Resources/Public/Icons/socialstream_domain_model_channel_soundcloud.svg',));
