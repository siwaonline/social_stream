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

use Socialstream\SocialStream\Utility\Feed\FeedUtility;    

/**
 * GetFeedCommandController
 */
class GetFeedCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController
{

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;


    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
     * @inject
     */
    protected $configurationManager;

    /**
     * The settings.
     * @var array
     */
    protected $settings = array();

    /**
     * channelRepository
     *
     * @var \Socialstream\SocialStream\Domain\Repository\ChannelRepository
     * @inject
     */
    protected $channelRepository = NULL;

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
     * @param int $rootPage
     *
     * @return bool
     */
    public function getFeedCommand($rootPage = 1)
    {
        FeedUtility::initTSFE($rootPage);
        $this->channelRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Socialstream\\SocialStream\\Domain\\Repository\\ChannelRepository');
        $this->newsRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Socialstream\\SocialStream\\Domain\\Repository\\NewsRepository');
        $this->categoryRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('GeorgRinger\\News\\Domain\\Repository\\CategoryRepository');
        $querySettings = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings');
        $querySettings->setRespectStoragePage(FALSE);
        $this->channelRepository->setDefaultQuerySettings($querySettings);
        $this->newsRepository->setDefaultQuerySettings($querySettings);
        
        $channels = $this->channelRepository->findAll();
        foreach ($channels as $channel){
            $utility = FeedUtility::getUtility($channel->getType(),$rootPage);
            $channel = $utility->renewToken($channel);
            $ch = $utility->getChannel($channel);
            $this->channelRepository->update($ch);
            $utility->getFeed($channel,$utility->settings["limitPosts"]);
        }
    }
}