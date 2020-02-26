<?php

namespace Socialstream\SocialStream\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 5678
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

use Socialstream\SocialStream\Domain\Model\Channel;
use Socialstream\SocialStream\Utility\BaseUtility;
use Socialstream\SocialStream\Utility\Feed\FeedUtility;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * GetFeedCommandController
 */
class GetFeedCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController
{
    /**
     * channelRepository
     *
     * @var \Socialstream\SocialStream\Domain\Repository\ChannelRepository
     * @inject
     */
    protected $channelRepository = NULL;
    /**
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     */
    protected $cacheManager = null;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;


    /**
     * @var array
     */
    protected $settings = array();

    /**
     * @param int $rootPage
     * @param string $storagePid
     */
    public function getFeedCommand($rootPage = 1, $storagePid = null)
    {
        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );

        FeedUtility::initTSFE($rootPage);
        $this->channelRepository = GeneralUtility::makeInstance('Socialstream\\SocialStream\\Domain\\Repository\\ChannelRepository');
        $this->cacheManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager');
        $querySettings = $this->channelRepository->createQuery()->getQuerySettings();
        if (!empty($storagePid)) {
            $querySettings->setStoragePageIds(explode(",", $storagePid));
        } else {
            $querySettings->setRespectStoragePage(FALSE);
        }
        $this->channelRepository->setDefaultQuerySettings($querySettings);
        $channels = $this->channelRepository->findAll();
        BaseUtility::log(__CLASS__, "info", "Found channels to crawl: " . $channels->count());
        /** @var Channel $channel */
        foreach ($channels as $channel) {
            BaseUtility::log(__CLASS__, 'info', "Started crawling channel " . ucfirst($channel->getType()) . " - " . ($channel->getTitle() ? $channel->getTitle() : $channel->getObjectId()));
            try {
                $utility = FeedUtility::getUtility($channel->getType(), $rootPage);
                if (!empty($storagePid)) {
                    $utility->setStoragePid($storagePid);
                } else {
                    $utility->setStoragePid($channel->getPid());
                }
                $channel = $utility->renewToken($channel);
                $ch = $utility->getChannel($channel);
                $this->channelRepository->update($ch);
                $utility->getFeed($channel, $utility->settings["limitPosts"]);
                BaseUtility::log(__CLASS__, 'info', "Finished crawling " . ucfirst($channel->getType()) . " - " . ($channel->getTitle() ? $channel->getTitle() : $channel->getObjectId()) . " successfully.");
            } catch (\Exception $e) {
                if ($this->settings['sysmail'] && $this->settings['sendermail']) {
                    /** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailView */
                    $errorEmailView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
                    $errorEmailView->setFormat('html');
                    $templatePathAndFilename = 'EXT:social_stream/Resources/Private/Templates/Email/Error.html';
                    $errorEmailView->setTemplatePathAndFilename($templatePathAndFilename);
                    $errorEmailView->assignMultiple(['error' => $e->getMessage(), 'channel' => $channel]);
                    $errorEmailContent = $errorEmailView->render();

                    /** @var MailMessage $email */
                    $email = GeneralUtility::makeInstance(MailMessage::class);
                    $email->setFrom($this->settings['sendermail'])->setTo($this->settings['sysmail'])->setSubject('Social Stream failed crawling channel: [' . $channel->getUid() . "] " . $channel->getTitle())->setBody($errorEmailContent, 'text/html')->send();
                }

                BaseUtility::log(__CLASS__, 'error', $e->getMessage());
                BaseUtility::log(__CLASS__, 'info', "Finished crawling " . ucfirst($channel->getType()) . " - " . ($channel->getTitle() ? $channel->getTitle() : $channel->getObjectId()) . " with an error.");
            }
        }
        $this->cacheManager->flushCachesByTag('tx_news');
    }
}