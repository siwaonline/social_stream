<?php

namespace Socialstream\SocialStream\Utility;


use Socialstream\SocialStream\Domain\Model\Channel;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;


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
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $persistenceManager;

    /**
     * newsRepository
     *
     * @var \Socialstream\SocialStream\Domain\Repository\NewsRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $newsRepository = NULL;

    /**
     * categoryRepository
     *
     * @var \GeorgRinger\News\Domain\Repository\CategoryRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
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
     * @param int $type
     */
    public static function initTSFE($id = 1, $type = 0)
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        if (!$_SERVER['HTTP_HOST']) $_SERVER['HTTP_HOST'] = "http://localhost";
        if (!$_SERVER['REQUEST_URI']) $_SERVER['REQUEST_URI'] = "/";
        $request = $GLOBALS['TYPO3_REQUEST'] ?? ServerRequestFactory::fromGlobals();
        $site = $request->getAttribute('site');
        if (!$site instanceof Site) {
            $sites = GeneralUtility::makeInstance(SiteFinder::class)->getAllSites();
            $site = reset($sites);
            if (!$site instanceof Site) {
                $site = new NullSite();
            }
        }
        $language = $request->getAttribute('language');
        if (!$language instanceof SiteLanguage) {
            $language = $site->getDefaultLanguage();
        }
        $GLOBALS['TSFE'] = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            GeneralUtility::makeInstance(Context::class),
            $site,
            $language,
            $request->getAttribute('routing', new PageArguments((int)$id, (string)$type, []))
        );
    }

    /**
     *
     */
    public function initSettings($pid)
    {
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        //$this->configurationManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
        //$this->settings = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Socialstream');
        $this->configurationManager = $this->objectManager->get(\Socialstream\SocialStream\Configuration\ConfigurationManager::class);
        $this->configurationManager->getConcreteConfigurationManager()->setCurrentPageId($pid);
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
            'facebookinvolve' => 'Facebook Involve Feed',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            'twitter' => 'Twitter',
            'flickr' => 'Flickr',
            'nextcloud' => 'Nextcloud',
            'googlephotos' => 'Google Photos'
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
