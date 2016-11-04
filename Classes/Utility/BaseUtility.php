<?php
namespace Socialstream\SocialStream\Utility;


    /***************************************************************
     *
     *  Copyright notice
     *
     *  (c) 2016
     *
     *  All rights reserved
     *
     *  This script is part of the TYPO3 project. The TYPO3 project is
     *  free software; you can redistribute it and/or modify
     *  it under the terms of the GNU General Public License as published by
     *  the Free Software Foundation; either version 3 of the License, or
     *  (at your option) any later version.
     *
     *  The GNU General Public License can be found at
     *  http://www.gnu.org/copyleft/gpl.html.
     *
     *  This script is distributed in the hope that it will be useful,
     *  but WITHOUT ANY WARRANTY; without even the implied warranty of
     *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *  GNU General Public License for more details.
     *
     *  This copyright notice MUST APPEAR in all copies of the script!
     ***************************************************************/

/**
 * Label
 */
class BaseUtility
{
    public static function initTSFE($id = 1, $typeNum = 0) {
        \TYPO3\CMS\Frontend\Utility\EidUtility::initTCA();
        if (!is_object($GLOBALS['TT'])) {
            $GLOBALS['TT'] = new \TYPO3\CMS\Core\TimeTracker\NullTimeTracker;
            $GLOBALS['TT']->start();
        }
        $GLOBALS['TSFE'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController',
            $GLOBALS['TYPO3_CONF_VARS'], $id, $typeNum);
        $GLOBALS['TSFE']->sys_page = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Page\\PageRepository');
        $GLOBALS['TSFE']->sys_page->init(TRUE);
        $GLOBALS['TSFE']->connectToDB();
        $GLOBALS['TSFE']->initFEuser();
        $GLOBALS['TSFE']->determineId();
        $GLOBALS['TSFE']->initTemplate();
        $GLOBALS['TSFE']->rootLine = $GLOBALS['TSFE']->sys_page->getRootLine($id, '');
        $GLOBALS['TSFE']->getConfigArray();

        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('realurl')) {
            $rootline = \TYPO3\CMS\Backend\Utility\BackendUtility::BEgetRootLine($id);
            $host = \TYPO3\CMS\Backend\Utility\BackendUtility::firstDomainRecord($rootline);
            $_SERVER['HTTP_HOST'] = $host;
        }
    }
    public function initSettings(){
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
        $this->settings = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Socialstream');
    }

    public static function getTypes(){
        return array(
            'facebook' => 'Facebook',
            'facebookevent' => 'Facebook Event',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            'twitter' => 'Twitter',
            'xing' => 'Xing',
            'linkedin' => 'LinkedIn',
            'soundcloud' => 'Soundcloud',
        );
    }
    public static function getType($key){
        $type = self::getTypes()[$key];
        if(!$type){
            return $key;
        }else{
            return $type;
        }
    }
    public static function getTypesTCA(){
        $arr = self::getTypes();
        $result = array();
        foreach ($arr as $k => $c){
            array_push($result,array($c,$k));
        }
        return $result;
    }
    public function get_http_response_code($url) {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }
    public function sendInfoMail($from,$to,$subject,$text){
        $message = (new \TYPO3\CMS\Core\Mail\MailMessage())
            ->setFrom($from)
            ->setTo($to)
            ->setSubject("=?utf-8?b?".base64_encode($subject)."?=")
            ->setBody($text,"text/html");
        $message->send();
        
    }

}