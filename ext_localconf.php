<?php
defined('TYPO3_MODE') or die();

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Socialstream\\SocialStream\\Controller\\GetFeedCommandController';

$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/News'][] = 'social_stream';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:social_stream/Classes/Hooks/ChannelProcessDatamap.php:Socialstream\SocialStream\Hooks\ChannelProcessDatamap';
