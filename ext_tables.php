<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Social Stream');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_socialstream_domain_model_channel', 'EXT:social_stream/Resources/Private/Language/locallang_csh_tx_socialstream_domain_model_channel.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_socialstream_domain_model_channel');

$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Imaging\\IconRegistry');
$iconRegistry->registerIcon('extensions-social-stream-empty', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', array("source" => 'EXT:social_stream/Resources/Public/Icons/socialstream_domain_model_channel_empty.svg',));
$iconRegistry->registerIcon('extensions-social-stream-facebook', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', array("source" => 'EXT:social_stream/Resources/Public/Icons/socialstream_domain_model_channel_facebook.svg',));
$iconRegistry->registerIcon('extensions-social-stream-instagram', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', array("source" => 'EXT:social_stream/Resources/Public/Icons/socialstream_domain_model_channel_instagram.svg',));
$iconRegistry->registerIcon('extensions-social-stream-youtube', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', array("source" => 'EXT:social_stream/Resources/Public/Icons/socialstream_domain_model_channel_youtube.svg',));
$iconRegistry->registerIcon('extensions-social-stream-twitter', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', array("source" => 'EXT:social_stream/Resources/Public/Icons/socialstream_domain_model_channel_twitter.svg',));
$iconRegistry->registerIcon('extensions-social-stream-xing', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', array("source" => 'EXT:social_stream/Resources/Public/Icons/socialstream_domain_model_channel_xing.svg',));
$iconRegistry->registerIcon('extensions-social-stream-linkedin', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', array("source" => 'EXT:social_stream/Resources/Public/Icons/socialstream_domain_model_channel_linkedin.svg',));
$iconRegistry->registerIcon('extensions-social-stream-flickr', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', array("source" => 'EXT:social_stream/Resources/Public/Icons/socialstream_domain_model_channel_flickr.svg',));
$iconRegistry->registerIcon('extensions-social-stream-soundcloud', 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider', array("source" => 'EXT:social_stream/Resources/Public/Icons/socialstream_domain_model_channel_soundcloud.svg',));
