<?php
namespace Socialstream\SocialStream\Controller;


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
use Socialstream\SocialStream\Domain\Model\Post;

/**
 * PageController
 */
class PageController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

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

    protected $fbappid = "";
    protected $fbappsecret = "";
    protected $instaappid = "";
    protected $instaappsecret = "";
    protected $ytappid = "";
    protected $ytappsecret = "";
    protected $twappid = "";
    protected $twappsecret = "";
    protected $xingappid = "";
    protected $xingappsecret = "";
    protected $liappid = "";
    protected $liappsecret = "";
    
    protected $limitPosts = "";
    protected $limitGalleries = "";
    protected $maxText = 0;
    protected $maxWidth = 0;
    protected $maxHeight = 0;
    protected $storagePid = 0;
    protected $tmp = "/tmp/";

    protected $viewType = "";
    protected $pages = "";
    protected $showpage = 0;
    protected $backpage = 0;
    protected $maxcount = 0;
    protected $pagination = 0;
    protected $maxposts = 0;
    protected $open = 0;
    protected $postStyle = 0;
    protected $nextprev = 0;

    public $rootPage = 1;

    public function initializeAction(){
        $this->fbappid = $this->settings['fbappid'];
        $this->fbappsecret = $this->settings['fbappsecret'];
        $this->instaappid = $this->settings['instaappid'];
        $this->instaappsecret = $this->settings['instaappsecret'];
        $this->ytappid = $this->settings['ytappid'];
        $this->ytappsecret = $this->settings['ytappsecret'];
        $this->twappid = $this->settings['twappid'];
        $this->twappsecret = $this->settings['twappsecret'];
        $this->xingappid = $this->settings['xingappid'];
        $this->xingappsecret = $this->settings['xingappsecret'];
        $this->liappid = $this->settings['liappid'];
        $this->liappsecret = $this->settings['liappsecret'];
        
        $this->limitPosts = $this->settings['limitPosts'];
        $this->limitGalleries = $this->settings['limitGalleries'];
        $this->maxText = $this->settings['maxText'];
        $this->maxWidth = $this->settings['maxWidth'];
        $this->maxHeight = $this->settings['maxHeight'];
        $this->storagePid = $this->settings['storagePid'];
        $this->tmp = $this->settings['tmp'];
        if(substr($this->tmp, -1) != "/")$this->tmp . "/";

        $this->viewType = $this->settings['flexview'];
        $this->pages = $this->settings['flexpages'];
        $this->showpage = $this->settings['flexshowpage'];
        if(!$this->showpage)$this->showpage = intval($GLOBALS['TSFE']->id);
        $this->backpage = $this->settings['flexbackpage'];
        if(!$this->backpage)$this->backpage = intval($GLOBALS['TSFE']->id);
        $this->maxcount = $this->settings['flexmaxcount'];
        $this->pagination = $this->settings['flexpagination'];
        $this->maxposts = $this->settings['flexmaxposts'];
        $this->open = $this->settings['flexopen'];
        $this->postStyle = $this->settings['flexstyle'];
        $this->nextprev = $this->settings['flexnextprev'];

    }
    
    /**
     * action list
     *
     * @return void
     */
    public function listAction(){
        if($_GET["tx_socialstream_pi1"]["viewType"]){
            $this->viewType = $_GET["tx_socialstream_pi1"]["viewType"];
        }
        $pageIds = explode(",",$this->pages);
        if($_GET["tx_socialstream_pi1"]["page"]){
            $pageIds = array($_GET["tx_socialstream_pi1"]["page"]);
        }
        if($_GET["tx_socialstream_pi1"]["viewType"]){
            $this->viewType = $_GET["tx_socialstream_pi1"]["viewType"];
        }
        if($this->viewType == 1) {
            $posts = $this->postRepository->getByPages($pageIds,(integer)$this->maxcount);
            $this->view->assign('posts', $posts);
        }
        if($this->viewType == 3) {
            $posts = $this->galleryRepository->getByPages($pageIds,(integer)$this->maxcount);
            $this->view->assign('posts', $posts);
        }
        if($this->viewType == 5) {
            $posts = $this->eventRepository->getByPages($pageIds,(integer)$this->maxcount);
            $this->view->assign('posts', $posts);
        }
        if($this->viewType == 2 || $this->viewType == 4 || $this->viewType == 6) {
            $pages = array();
            foreach ($pageIds as $key => $pageId) {
                array_push($pages, $this->pageRepository->findByUid($pageId));
            }
            $this->view->assign('posts', $pages);
        }

        if($this->maxposts < 1)$this->maxposts = 1;
        $this->view->assign('maxText', $this->maxText);
        $this->view->assign('maxWidth', $this->maxWidth);
        $this->view->assign('maxHeight', $this->maxHeight);

        $this->view->assign('showpage', $this->showpage);
        $this->view->assign('pagination', $this->pagination);
        $this->view->assign('maxposts', $this->maxposts);
        $this->view->assign('open', $this->open);
        $this->view->assign('postStyle', $this->postStyle);

        $this->view->assign('viewType', $this->viewType);
    }

    /**
     * action listbe
     *
     * @return void
     */
    public function listbeAction()
    {
        $pages = $this->pageRepository->findAll();
        $this->view->assign('pages', $pages);

        if($this->fbappid && $this->fbappsecret){
            $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 'TYPO3\CMS\Extbase\Object\ObjectManager' );
            $uriBuilder = $objectManager->get( 'TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder' );
            $uriBuilder->initializeObject();

            $fburl = "https://www.facebook.com/v2.4/dialog/oauth?client_id=".$this->fbappid."&state=".$this->fbappsecret."&response_type=token&scope=user_posts&sdk=php-sdk-5.0.0&redirect_uri=";
            $this->view->assign('fburl', $fburl);
        }
    }    
    
    /**
     * action show
     *
     * @param \Socialstream\SocialStream\Domain\Model\Page $page
     * @return void
     */
    public function showAction(\Socialstream\SocialStream\Domain\Model\Page $page)
    {
        $this->view->assign('page', $page);
    }

    /**
     * action showSinglePost
     *
     * @param \Socialstream\SocialStream\Domain\Model\Post $post
     * @return void
     */
    public function showSinglePostAction(\Socialstream\SocialStream\Domain\Model\Post $post)
    {
        if($this->nextprev){
            $prev = $this->postRepository->getPrev($post->getPage(),$post->getCreatedTime()->setTimeZone(new \DateTimeZone('UTC'))->format("Y-m-d H:i:s"));
            $next = $this->postRepository->getNext($post->getPage(),$post->getCreatedTime()->setTimeZone(new \DateTimeZone('UTC'))->format("Y-m-d H:i:s"));
            $this->view->assign('prev', $prev);
            $this->view->assign('next', $next);
        }
        $this->view->assign('post', $post);
        $this->view->assign('backpage', $this->backpage);
        $this->view->assign('nextprev', $this->nextprev);
    }

    /**
     * action showSingleEvent
     *
     * @param \Socialstream\SocialStream\Domain\Model\Event $post
     * @return void
     */
    public function showSingleEventAction(\Socialstream\SocialStream\Domain\Model\Event $post)
    {
        if($this->nextprev){
            $prev = $this->eventRepository->getPrev($post->getPage(),$post->getStartTime()->setTimeZone(new \DateTimeZone('UTC'))->format("Y-m-d H:i:s"));
            $next = $this->eventRepository->getNext($post->getPage(),$post->getStartTime()->setTimeZone(new \DateTimeZone('UTC'))->format("Y-m-d H:i:s"));
            $this->view->assign('prev', $prev);
            $this->view->assign('next', $next);
        }
        $this->view->assign('post', $post);
        $this->view->assign('backpage', $this->backpage);
        $this->view->assign('nextprev', $this->nextprev);
    }

    /**
     * action showbe
     *
     * @param \Socialstream\SocialStream\Domain\Model\Page $page
     * @return void
     */
    public function showbeAction(\Socialstream\SocialStream\Domain\Model\Page $page)
    {
        $this->view->assign('page', $page);
    }
    
    /**
     * action new
     *
     * @return void
     */
    public function newAction()
    {
        
    }
    
    /**
     * action create
     *
     * @param \Socialstream\SocialStream\Domain\Model\Page $newPage
     * @return void
     */
    public function createAction(\Socialstream\SocialStream\Domain\Model\Page $newPage)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->pageRepository->add($newPage);
        $this->redirect('list');
    }
    
    /**
     * action edit
     *
     * @param \Socialstream\SocialStream\Domain\Model\Page $page
     * @ignorevalidation $page
     * @return void
     */
    public function editAction(\Socialstream\SocialStream\Domain\Model\Page $page)
    {
        $this->view->assign('page', $page);
    }
    
    /**
     * action update
     *
     * @param \Socialstream\SocialStream\Domain\Model\Page $page
     * @return void
     */
    public function updateAction(\Socialstream\SocialStream\Domain\Model\Page $page)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->pageRepository->update($page);
        $this->redirect('list');
    }
    
    /**
     * action delete
     *
     * @param \Socialstream\SocialStream\Domain\Model\Page $page
     * @return void
     */
    public function deleteAction(\Socialstream\SocialStream\Domain\Model\Page $page)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->pageRepository->remove($page);
        $this->redirect('list');
    }

    /**
     * action message
     *
     * @param string $head
     * @param string $message
     * @param int $short
     * @return void
     */
    public function messageAction($head, $message, $close=0)
    {
        $this->view->assign('head', $head);
        $this->view->assign('message', $message);
        $this->view->assign('close', $close);
    }    
}