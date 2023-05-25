<?php
defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/News'][] = 'social_stream';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'Socialstream\\SocialStream\\Hooks\\ChannelProcessDatamap';

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1548167578] = [
    'nodeName' => 'tokenNode',
    'priority' => 30,
    'class' => \Socialstream\SocialStream\Nodes\TokenNode::class
];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][time()] = [
    'nodeName' => 'eidNode',
    'priority' => 40,
    'class' => \Socialstream\SocialStream\Nodes\EidNode::class,
];

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['generate_token'] = \Socialstream\SocialStream\Controller\EidController::class . '::generateTokenAction';

$GLOBALS['TYPO3_CONF_VARS']['LOG']['Socialstream']['SocialStream']['writerConfiguration'] = array(
    \TYPO3\CMS\Core\Log\LogLevel::INFO => array(
        'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => array(
            'logFile' => 'fileadmin/social_stream/logs/social_stream.txt'
        )
    )
);

// @TODO remove if not necessary
//$extbaseObjectContainer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class);
//$extbaseObjectContainer->registerImplementation(\GeorgRinger\News\Domain\Model\News::class, \Socialstream\SocialStream\Domain\Model\News::class);

// @TODO check
//$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager::class] = [
//    'className' => \Socialstream\SocialStream\Configuration\BackendConfigurationManager::class
//];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class] = [
    'className' => \Socialstream\SocialStream\Configuration\ConfigurationManager::class
];

unset($extbaseObjectContainer);
