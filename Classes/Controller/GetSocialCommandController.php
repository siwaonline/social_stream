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


/**
 * GetSocialCommandController
 */
class GetSocialCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController
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
     * pageRepository
     *
     * @var \Socialstream\SocialStream\Domain\Repository\PageRepository
     * @inject
     */
    protected $pageRepository = NULL;

    /**
     * postRepository
     *
     * @var \Socialstream\SocialStream\Domain\Repository\PostRepository
     * @inject
     */
    protected $postRepository = NULL;

    /**
     * galleryRepository
     *
     * @var \Socialstream\SocialStream\Domain\Repository\GalleryRepository
     * @inject
     */
    protected $galleryRepository = NULL;

    /**
     * eventRepository
     *
     * @var \Socialstream\SocialStream\Domain\Repository\EventRepository
     * @inject
     */
    protected $eventRepository = NULL;


    /**
     * @param int $rootPage
     *
     * @return bool
     */
    public function getSocialCommand($rootPage = 1)
    {
        $fbController = new \Socialstream\SocialStream\Controller\FacebookController();
        $fbController->rootPage = $rootPage;
        $fbController->getFacebookAction();
        $xingController = new \Socialstream\SocialStream\Controller\XingController();
        $xingController->rootPage = $rootPage;
        $xingController->getXingAction();
        $liController = new \Socialstream\SocialStream\Controller\LinkedInController();
        $liController->rootPage = $rootPage;
        $liController->getLinkedInAction();
    }
}