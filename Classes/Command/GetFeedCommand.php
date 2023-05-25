<?php

namespace Socialstream\SocialStream\Command;

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
use Socialstream\SocialStream\Domain\Repository\ChannelRepository;
use Socialstream\SocialStream\Utility\BaseUtility;
use Socialstream\SocialStream\Utility\Feed\FeedUtility;
use TYPO3\CMS\Core\Mail\MailMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * GetFeedCommandController
 */
class GetFeedCommand extends Command
{
    protected static $defaultName = 'socialstream:getfeed';

    /**
     * channelRepository
     *
     * @var \Socialstream\SocialStream\Domain\Repository\ChannelRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $channelRepository = NULL;

    /**
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     */
    protected $cacheManager = null;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $configurationManager;

    /**
     * @var array
     */
    protected $settings = [];

    protected function configure()
    {
        $this->setDescription('Gets all Social Stream Feeds.');

        $this->addArgument('rootPage', InputArgument::REQUIRED, 'rootPage');
        $this->addArgument('storagePid', InputArgument::REQUIRED, 'storagePid');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
//        $this->configurationManager->getConcreteConfigurationManager()->setCurrentPageId($input->getArgument('rootPage'));
        $this->settings = $this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Socialstream');


        $this->channelRepository = GeneralUtility::makeInstance(ChannelRepository::class);
        $this->cacheManager = GeneralUtility::makeInstance(CacheManager::class);

        $querySettings = $this->channelRepository->createQuery()->getQuerySettings();

        if (!empty($input->getArgument('storagePid'))) {
            $querySettings->setStoragePageIds(explode(",", $input->getArgument('storagePid')));
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
                $utility = FeedUtility::getUtility($channel->getType(), $input->getArgument('rootPage'));
                if (!empty($input->getArgument('storagePid'))) {
                    $utility->setStoragePid($input->getArgument('storagePid'));
                } else {
                    $utility->setStoragePid($channel->getPid());
                }
                $channel = $utility->renewToken($channel);
                $ch = $utility->getChannel($channel);
                $this->channelRepository->update($ch);
                $utility->getFeed($channel, $utility->settings["limitPosts"]);

                BaseUtility::log(__CLASS__, 'info', "Finished crawling " . ucfirst($channel->getType()) . " - " . ($channel->getTitle() ? $channel->getTitle() : $channel->getObjectId()) . " successfully.");
            } catch (\Exception $e) {
                throw $e;
                if ($this->settings['sysmail'] && $this->settings['sendermail']) {
                    /** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailView */
                    $errorEmailView = GeneralUtility::makeInstance(StandaloneView::class);
                    $errorEmailView->setFormat('html');
                    $templatePathAndFilename = 'EXT:social_stream/Resources/Private/Templates/Email/Error.html';
                    $errorEmailView->setTemplatePathAndFilename($templatePathAndFilename);
                    $errorEmailView->assignMultiple(['error' => $e->getMessage(), 'channel' => $channel]);
                    $errorEmailContent = $errorEmailView->render();

                    /** @var MailMessage $email */
                    $email = GeneralUtility::makeInstance(MailMessage::class);
                    $email->setFrom($this->settings['sendermail'])
                        ->setTo($this->settings['sysmail'])
                        ->setSubject('Social Stream failed crawling channel: [' . $channel->getUid() . "] " . $channel->getTitle())
                        ->html($errorEmailContent)
                        ->send();
                }

                BaseUtility::log(__CLASS__, 'error', $e->getMessage());
                BaseUtility::log(__CLASS__, 'info', "Finished crawling " . ucfirst($channel->getType()) . " - " . ($channel->getTitle() ? $channel->getTitle() : $channel->getObjectId()) . " with an error.");

                return Command::FAILURE;
            }
        }
        $this->cacheManager->flushCachesByTag('tx_news');

        return Command::SUCCESS;
    }
}
