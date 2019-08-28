<?php

namespace Socialstream\SocialStream\Utility;


use Socialstream\SocialStream\Domain\Model\Channel;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

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
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * newsRepository
     *
     * @var \Socialstream\SocialStream\Domain\Repository\NewsRepository
     * @inject
     */
    protected $newsRepository = NULL;

    /**
     * categoryRepository
     *
     * @var \GeorgRinger\News\Domain\Repository\CategoryRepository
     * @inject
     */
    protected $categoryRepository = NULL;

    /**
     * @var int
     */
    protected $storagePid = 0;

    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->persistenceManager = GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
        $this->newsRepository = $this->objectManager->get("Socialstream\\SocialStream\\Domain\\Repository\\NewsRepository");
        $this->categoryRepository = $this->objectManager->get("GeorgRinger\\News\\Domain\\Repository\\CategoryRepository");

        $querySettings = $this->newsRepository->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(FALSE);
        $this->newsRepository->setDefaultQuerySettings($querySettings);

        $querySettings = $this->categoryRepository->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(FALSE);
        $this->categoryRepository->setDefaultQuerySettings($querySettings);
    }

    /**
     * @return int
     */
    public function getStoragePid()
    {
        return $this->storagePid;
    }

    /**
     * @param int $storagePid
     */
    public function setStoragePid($storagePid)
    {
        $this->storagePid = $storagePid;
    }

    /**
     * @param int $id
     * @param int $typeNum
     */
    public static function initTSFE($id = 1, $typeNum = 0)
    {
        \TYPO3\CMS\Frontend\Utility\EidUtility::initTCA();
        if (!is_object($GLOBALS['TT'])) {
            $GLOBALS['TT'] = new \TYPO3\CMS\Core\TimeTracker\TimeTracker();
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
    }

    /**
     *
     */
    public function initSettings()
    {
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->configurationManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
        $this->settings = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Socialstream');

    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return array(
            'facebook' => 'Facebook',
            'facebookevent' => 'Facebook Event',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            'twitter' => 'Twitter',
            'flickr' => 'Flickr'
            /*'xing' => 'Xing',
            'linkedin' => 'LinkedIn',
            'soundcloud' => 'Soundcloud',*/
        );
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function getType($key)
    {
        $type = self::getTypes()[$key];
        if (!$type) {
            return $key;
        } else {
            return $type;
        }
    }

    /**
     * @return array
     */
    public static function getTypesTCA()
    {
        $arr = self::getTypes();
        $result = array();
        foreach ($arr as $k => $c) {
            array_push($result, array($c, $k));
        }
        return $result;
    }

    /**
     * @param $url
     * @return bool|string
     */
    public function get_http_response_code($url)
    {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }

    /**
     * @param $from
     * @param $to
     * @param $subject
     * @param $text
     */
    public function sendInfoMail($from, $to, $subject, $text)
    {
        $message = (new \TYPO3\CMS\Core\Mail\MailMessage())
            ->setFrom($from)
            ->setTo($to)
            ->setSubject("=?utf-8?b?" . base64_encode($subject) . "?=")
            ->setBody($text, "text/html");
        $message->send();

    }

    /**
     * @param $mediaUrl
     * @return bool
     */
    public function validateMediaAvailability($mediaUrl)
    {
        if ($this->get_http_response_code($mediaUrl) == 200) {
            return true;
        }
        return false;
    }

    /**
     * @param $mediaUrl
     * @return bool
     */
    public function validateMediaSize($mediaUrl)
    {
        $maxFilesize = $this->settings["maxFilesize"] * 1024 * 1024;
        $head = array_change_key_case(get_headers($mediaUrl, TRUE));
        if (array_key_exists('content-length', $head)) {
            $filesize = $head['content-length'];
            if ($filesize <= $maxFilesize && $filesize > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Channel $channel
     * @param string $imageUrl
     * @param string|null $videoUrl
     * @return array
     */
    public function validateMedia($channel, $imageUrl, $videoUrl = null)
    {
        $link = '';
        $mediaUrl = '';

        if ($videoUrl && $imageUrl) {
            if ($channel->isVideosync()) {
                if ($this->validateMediaAvailability($videoUrl)) {
                    if ($this->validateMediaSize($videoUrl)) {
                        $mediaUrl = $videoUrl;
                    } else {
                        if ($this->validateMediaAvailability($imageUrl)) {
                            if ($this->validateMediaSize($imageUrl)) {
                                $mediaUrl = $imageUrl;
                            }
                        }
                    }
                }
            } else {
                if ($this->validateMediaAvailability($imageUrl)) {
                    if ($this->validateMediaSize($imageUrl)) {
                        $mediaUrl = $imageUrl;
                    }
                }
            }
            $link = $videoUrl;
        } else if ($imageUrl) {
            if ($this->validateMediaAvailability($imageUrl)) {
                if ($this->validateMediaSize($imageUrl)) {
                    $mediaUrl = $imageUrl;
                }
            }
            $link = $imageUrl;
        }

        return array('link' => $link, 'media_url' => $mediaUrl);
    }

    /**
     * @param string $class
     * @param string $method
     * @param string $message
     */
    public static function log($class = __CLASS__, $method = 'debug', $message = "Empty Logmessage")
    {
        $logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Log\LogManager::class)->getLogger($class);
        if (method_exists($logger, $method)) {
            $logger->{$method}($message);
        }
    }
}