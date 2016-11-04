<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Social Stream');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_socialstream_domain_model_channel', 'EXT:social_stream/Resources/Private/Language/locallang_csh_tx_socialstream_domain_model_channel.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_socialstream_domain_model_channel');

$icons = array(
	"facebook" => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('social_stream') . 'Resources/Public/Icons/socialstream_domain_model_channel_facebook.svg',
	"instagram" => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('social_stream') . 'Resources/Public/Icons/socialstream_domain_model_channel_instagram.svg',
	"youtube" => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('social_stream') . 'Resources/Public/Icons/socialstream_domain_model_channel_youtube.svg',
	"twitter" => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('social_stream') . 'Resources/Public/Icons/socialstream_domain_model_channel_twitter.svg',
	"xing" => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('social_stream') . 'Resources/Public/Icons/socialstream_domain_model_channel_xing.svg',
	"linkedin" => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('social_stream') . 'Resources/Public/Icons/socialstream_domain_model_channel_linkedin.svg',
	"soundcloud" => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('social_stream') . 'Resources/Public/Icons/socialstream_domain_model_channel_soundcloud.svg',
);

\TYPO3\CMS\Backend\Sprite\SpriteManager::addSingleIcons($icons,"social_stream");