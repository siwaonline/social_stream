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

/**
 * FacebookController
 */
class FacebookController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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
    protected $limitPosts = "";
    protected $limitGalleries = "";
    protected $maxText = 0;
    protected $maxWidth = 0;
    protected $maxHeight = 0;
    protected $storagePid = 0;
    protected $tmp = "/tmp/";
    protected $clearStrings = array('\ud83c\u','\ud83d\u','\u2600\u');

    public $rootPage = 1;
    
    protected $streamtype = 1;

    public function initializeAction(){
        $this->fbappid = $this->settings['fbappid'];
        $this->fbappsecret = $this->settings['fbappsecret'];
        $this->limitPosts = $this->settings['limitPosts'];
        $this->limitGalleries = $this->settings['limitGalleries'];
        $this->maxText = $this->settings['maxText'];
        $this->maxWidth = $this->settings['maxWidth'];
        $this->maxHeight = $this->settings['maxHeight'];
        $this->storagePid = $this->settings['storagePid'];
        $this->tmp = $this->settings['tmp'];
        if(substr($this->tmp, -1) != "/")$this->tmp . "/";
    }
        
    /**
     * action token
     *
     * @return void
     */
    public function tokenAction()
    {
        $vars = $this->request->getArguments();
        $pagename = $vars["page"]["name"];
        if($pagename) {
            if ($pagename != "me") {
                $dbpage = $this->pageRepository->searchByName($pagename,$this->streamtype);
                if ($dbpage->toArray()) {
                    $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.already', 'social_stream');
                    $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error', 'social_stream');
                    $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg));
                }else{
                    $headers = get_headers("https://www.facebook.com/".$pagename);
                    $resp = substr($headers[0], 9, 3);
                    if($resp != 200 && $resp != 302 && $resp != 999){
                        $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.nopage', 'social_stream');
                        $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error', 'social_stream');
                        $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg));
                    }
                }
            }
        }
        
        $accesstoken = $_GET["access_token"];
        if($accesstoken) {
            $token = file_get_contents("https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id=" . $this->fbappid . "&client_secret=" . $this->fbappsecret . "&fb_exchange_token=" . $accesstoken);
            $infos = explode("&",$token);
            $tk = explode("=",$infos[0])[1];
            $exp = explode("=",$infos[1])[1];
            $page = new \Socialstream\SocialStream\Domain\Model\Page();
            $page->setName($_GET["name"]);
            $page->setStreamtype(1);
            $page->setToken($tk);
            $page->setExpires(time()+$exp);
            //$this->pageRepository->add($page);
            //$persistenceManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
            //$persistenceManager->persistAll();
            $this->forward('create', null, null, array('page' => $page, 'short' => 1, 'close' => 1));
            //$this->view->assign('close', 1);
        }else{
            if(!$_GET["name"]) {
                $accessurl = "https://www.facebook.com/v2.4/dialog/oauth?client_id=" . $this->fbappid . "&state=" . $this->fbappsecret . "&response_type=token&scope=user_posts&sdk=php-sdk-5.0.0&redirect_uri=";
                $this->view->assign('accessurl', $accessurl);
                $this->view->assign('name', $pagename);
            }else{
                $this->view->assign('name', $_GET["name"]);
            }
        }        
    }

    /**
     * action create
     *
     * @param \Socialstream\SocialStream\Domain\Model\Page $page
     * @param int $short
     * @param int $close
     * @return void
     */
    public function createAction(\Socialstream\SocialStream\Domain\Model\Page $page, $short = 0, $close = 0)
    {
        if($page->getName()){
            $already = 0;
            if($page->getName() != "me") {
                $dbpage = $this->pageRepository->searchByName($page->getName(),$this->streamtype);
                if ($dbpage->toArray()) {
                    $already = 1;
                    $page = $dbpage[0];
                }
            }

            $storageRepository = $this->objectManager->get('TYPO3\\CMS\\Core\\Resource\\StorageRepository');
            $clear = 0;
            $storage = $storageRepository->findByUid('1');
            if($storage->hasFolder("facebook")){
                $targetFolder = $storage->getFolder('facebook');
            }else{
                $targetFolder = $storage->createFolder('facebook');
            }

            try {
                // ### get Page Data ###
                $page = $this->pageProcess($page,$storage,$targetFolder,$already);
                if($targetFolder->hasFolder($page->getId())){
                    $subFolder = $targetFolder->getSubfolder($page->getId());
                }else{
                    $subFolder = $targetFolder->createFolder($page->getId());
                }

                // ### get Posts of Page ###
                if($subFolder->hasFolder("posts")){
                    $postsFolder = $subFolder->getSubfolder("posts");
                }else{
                    $postsFolder = $subFolder->createFolder("posts");
                }
                $clear += $this->postProcess($page,$storage,$targetFolder,$subFolder,$postsFolder,$short);

                // ### get Gallery Page ###
                if($subFolder->hasFolder("gallery")){
                    $galleryFolder = $subFolder->getSubfolder("gallery");
                }else{
                    $galleryFolder = $subFolder->createFolder("gallery");
                }
                $clear += $this->galleryProcess($page,$storage,$targetFolder,$subFolder,$galleryFolder,$short);

                // ### get Event Page ###
                if($subFolder->hasFolder("events")){
                    $eventFolder = $subFolder->getSubfolder("events");
                }else{
                    $eventFolder = $subFolder->createFolder("events");
                }
                $clear += $this->eventProcess($page,$storage,$targetFolder,$subFolder,$eventFolder,$short);

                if($clear > 0){
                    $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler');
                    $tce->clear_cacheCmd('cacheTag:socialStream');
                }

                if($close) {
                    $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.created', 'social_stream');
                }else{
                    $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.imported', 'social_stream');
                }
                $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.success', 'social_stream');
                $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg, 'close' => $close));

            } catch (\TYPO3\CMS\Core\Error\Exception $e) {
                $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.nodata', 'social_stream');
                $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error', 'social_stream');
                //$this->redirect('message', null, null, array('head' => $head, 'message' => $e->getMessage()));
                $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg, 'close' => $close));
            }

        }else{
            $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.noname','social_stream');
            $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error','social_stream');
            $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg, 'close' => $close));
        }
    }

    /**
     * action getFacebook
     *
     * @return void
     */
    public function getFacebookAction()
    {
        $this->initTSFE($this->rootPage,0);
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
        $this->settings = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Socialstream');
        $this->pageRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Socialstream\\SocialStream\\Domain\\Repository\\PageRepository');
        $this->postRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Socialstream\\SocialStream\\Domain\\Repository\\PostRepository');
        $this->galleryRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Socialstream\\SocialStream\\Domain\\Repository\\GalleryRepository');
        $this->initializeAction();
        $short = 0;
        $pages = $this->pageRepository->findAll();
        $clear = 0;

        foreach($pages as $page){
            $storageRepository = $this->objectManager->get('TYPO3\\CMS\\Core\\Resource\\StorageRepository');
            $storage = $storageRepository->findByUid('1');
            if($storage->hasFolder("facebook")){
                $targetFolder = $storage->getFolder('facebook');
            }else{
                $targetFolder = $storage->createFolder('facebook');
            }

            try {
                // ### get Page Data ###
                $page = $this->pageProcess($page,$storage,$targetFolder,1,0);
                if($targetFolder->hasFolder($page->getId())){
                    $subFolder = $targetFolder->getSubfolder($page->getId());
                }else{
                    $subFolder = $targetFolder->createFolder($page->getId());
                }

                // ### get Posts of Page ###
                if($subFolder->hasFolder("posts")){
                    $postsFolder = $subFolder->getSubfolder("posts");
                }else{
                    $postsFolder = $subFolder->createFolder("posts");
                }
                $clear += $this->postProcess($page,$storage,$targetFolder,$subFolder,$postsFolder,$short);

                // ### get Gallery Page ###
                if($subFolder->hasFolder("gallery")){
                    $galleryFolder = $subFolder->getSubfolder("gallery");
                }else{
                    $galleryFolder = $subFolder->createFolder("gallery");
                }
                $clear += $this->galleryProcess($page,$storage,$targetFolder,$subFolder,$galleryFolder,$short);

                // ### get Event Page ###
                if($subFolder->hasFolder("events")){
                    $eventFolder = $subFolder->getSubfolder("events");
                }else{
                    $eventFolder = $subFolder->createFolder("events");
                }
                $clear += $this->eventProcess($page,$storage,$targetFolder,$subFolder,$eventFolder,$short);

            } catch (\TYPO3\CMS\Core\Error\Exception $e) {
                echo "".$e->getMessage();
            }
        }
        
        if($clear > 0){
            $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler');
            $tce->clear_cacheCmd('cacheTag:socialStream');
        }

    }

    private function pageProcess($page,$storage,$targetFolder,$already,$showerror=1){
        $persistenceManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');

        if(!$this->fbappid || !$this->fbappsecret){
            $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.noapp', 'social_stream');
            $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error', 'social_stream');
            if($showerror) {
                $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg, 'close' => 1));
            }else{
                return $page;
            }
        }
        if(!$this->storagePid){
            $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.nostorage', 'social_stream');
            $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error', 'social_stream');
            if($showerror) {
                $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg, 'close' => 1));
            }else{
                return $page;
            }
        }

        $tk = $page->getToken();
        $expdiff = ($page->getExpires() - time())/86400;
        if($expdiff > 0 && $expdiff <= 5 && $tk){
            $token = file_get_contents("https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id=" . $this->fbappid . "&client_secret=" . $this->fbappsecret . "&fb_exchange_token=" . $tk);
            $infos = explode("&",$token);
            $tk = explode("=",$infos[0])[1];
            $exp = explode("=",$infos[1])[1];
            $page->setToken($tk);
        }
        if(!$tk){
            $tk = $this->fbappid."|".$this->fbappsecret;
        }
        if ($page->getName() == "me"){
            $page->setMe(1);
        }
        try {
            if ($page->getMe()) {
                $elem = (file_get_contents("https://graph.facebook.com/me/?fields=id,name,about,link,picture,cover&access_token=$tk"));
            } else {
                if($page->getId()) {
                    $elem = (file_get_contents("https://graph.facebook.com/" . $page->getId() . "/?fields=id,name,about,description,link,picture,cover&access_token=$tk"));
                }else{
                    $elem = (file_get_contents("https://graph.facebook.com/" . $page->getName() . "/?fields=id,name,about,description,link,picture,cover&access_token=$tk"));
                }
            }

        } catch (\TYPO3\CMS\Core\Error\Exception $e) {
            $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.nodata', 'social_stream');
            $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error', 'social_stream');
            if($showerror) {
                $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg, 'close' => 1));
            }else{
                return $page;
            }
        }
        if(!$elem){
            $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.nodata', 'social_stream');
            $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error', 'social_stream');
            if($showerror) {
                $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg, 'close' => 1));
            }else{
                return $page;
            }
        }
        foreach ($this->clearStrings as $str) {
            while(strpos($elem, $str) !== false){
                $pos = strpos($elem, $str);
                $elem = substr_replace($elem,'',$pos,12);
            }
        }

        $elem = json_decode($elem);

        $helppage = $this->pageRepository->searchById($elem->id,$this->streamtype);
        if($helppage){
            $helppage->setToken($page->getToken());
            $helppage->setExpires($page->getExpires());
            $page = $helppage;
            $already = 1;
        }


        $page->setId($elem->id);
        $page->setName($elem->name);
        if($elem->about)$page->setAbout($elem->about);
        if($elem->description)$page->setDescription($elem->description);
        $page->setLink($elem->link);
        $page->setCoverUrl($elem->cover->source);
        $page->setStreamtype($this->streamtype);

        if($page->getMe()){
            //$bildurl = $elem->picture->data->url;
            $picstream = json_decode(file_get_contents("https://graph.facebook.com/me/picture?redirect=0&width=900&access_token=$tk"));
            $bildurl = $picstream->data->url;
        }else {
            if ($page->getId()) {
                $picstream = json_decode(file_get_contents("https://graph.facebook.com/" . $page->getId() . "/photos?fields=source&access_token=$tk"));
            } else {
                $picstream = json_decode(file_get_contents("https://graph.facebook.com/" . $page->getName() . "/photos?fields=source&access_token=$tk"));
            }
            $bildurl = $picstream->data[0]->source;
        }

        $bildname = explode("?",basename($bildurl));
        if (is_array($bildname)){
            $bildname = $bildname[0];
        }
        $page->setPictureUrl($bildurl);

        if($already==1) {
            $this->pageRepository->update($page);
        }else{
            $this->pageRepository->add($page);
        }
        $persistenceManager->persistAll();

        if($targetFolder->hasFolder($page->getId())){
            $subFolder = $targetFolder->getSubfolder($page->getId());
        }else{
            $subFolder = $targetFolder->createFolder($page->getId());
        }

        if(!$subFolder->hasFile($bildname)) {
            if ($bildurl) {
                copy($bildurl, $this->tmp . $bildname);
                $movedNewFile = $storage->addFile($this->tmp . $bildname, $subFolder, $bildname);
                $bild = $movedNewFile->getUid();
            }
            if($page->getPicture()){
                $GLOBALS["TYPO3_DB"]->exec_UPDATEquery("sys_file_reference","uid=".$page->getPicture()->getUid(),array('deleted'=>'1'));
            }
        }elseif(!$page->getPicture()){
            $bild = $storage->getFile("/".$targetFolder->getName()."/".$subFolder->getName()."/".$bildname);
            $bild = $bild->getUid();
        }

        if ($bild) {
            $data = array();
            $data['sys_file_reference']['NEW123'] = array(
                'uid_local' => $bild,
                'uid_foreign' => $page->getUid(), // uid of your content record
                'tablenames' => '	tx_socialstream_domain_model_page',
                'fieldname' => 'picture',
                'pid' => $this->storagePid, // parent id of the parent page
                'table_local' => 'sys_file',
            );
            $data['tx_socialstream_domain_model_page'][$page->getUid()] = array('picture' => 'NEW123'); // set to the number of images?

            /** @var \TYPO3\CMS\Core\DataHandling\DataHandler $tce */
            $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler'); // create TCE instance
            $tce->bypassAccessCheckForRecords = TRUE;
            $tce->start($data, array());
            $tce->admin = TRUE;
            $tce->process_datamap();
        }

        $bild = NULL;
        $bildurl = $page->getCoverUrl();
        $bildname = explode("?",basename($bildurl));
        if (is_array($bildname)){
            $bildname = $bildname[0];
        }
        if(!$subFolder->hasFile($bildname)) {
            if ($bildurl) {
                copy($bildurl, $this->tmp . $bildname);
                $movedNewFile = $storage->addFile($this->tmp . $bildname, $subFolder, $bildname);
                $bild = $movedNewFile->getUid();
            }
            if($page->getCover()){
                $GLOBALS["TYPO3_DB"]->exec_UPDATEquery("sys_file_reference","uid=".$page->getCover()->getUid(),array('deleted'=>'1'));
            }
        }elseif(!$page->getCover()){
            $bild = $storage->getFile("/".$targetFolder->getName()."/".$subFolder->getName()."/".$bildname);
            $bild = $bild->getUid();
        }
        if ($bild) {
            $data = array();
            $data['sys_file_reference']['NEW1234'] = array(
                'uid_local' => $bild,
                'uid_foreign' => $page->getUid(), // uid of your content record
                'tablenames' => 'tx_socialstream_domain_model_page',
                'fieldname' => 'cover',
                'pid' => $this->storagePid, // parent id of the parent page
                'table_local' => 'sys_file',
            );
            $data['tx_socialstream_domain_model_page'][$page->getUid()] = array('cover' => 'NEW1234'); // set to the number of images?

            $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler'); // create TCE instance
            $tce->bypassAccessCheckForRecords = TRUE;
            $tce->start($data, array());
            $tce->admin = TRUE;
            $tce->process_datamap();
        }

        return $page;
    }

    private function postProcess($page,$storage,$targetFolder,$subFolder,$postsFolder,$short,$paging=""){
        $persistenceManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
        $clear = 0;

        if($paging){
            $stream = (file_get_contents($paging));
        }else {
            $tk = $page->getToken();
            if(!$tk){
                $tk = $this->fbappid."|".$this->fbappsecret;
            }
            if ($short) {
                if ($page->getMe()) {
                    $stream = (file_get_contents("https://graph.facebook.com/me/feed?fields=id,created_time,link,type,message,full_picture,object_id,picture,name,caption,description,story,source&access_token=$tk&limit=$this->limitPosts"));
                } else {
                    if($page->getId()){
                        $stream = (file_get_contents("https://graph.facebook.com/" . $page->getId() . "/feed?fields=id,created_time,link,type,message,full_picture,object_id,picture,name,caption,description,story,source&access_token=$tk&limit=$this->limitPosts"));
                    }else {
                        $stream = (file_get_contents("https://graph.facebook.com/" . $page->getName() . "/feed?fields=id,created_time,link,type,message,full_picture,object_id,picture,name,caption,description,story,source&access_token=$tk&limit=$this->limitPosts"));
                    }
                }
            } else {
                if ($page->getMe()) {
                    $stream = (file_get_contents("https://graph.facebook.com/me/feed?fields=id,created_time,link,type,message,full_picture,object_id,picture,name,caption,description,story,source&access_token=$tk"));
                } else {
                    if($page->getId()){
                        $stream = (file_get_contents("https://graph.facebook.com/" . $page->getId() . "/feed?fields=id,created_time,link,type,message,full_picture,object_id,picture,name,caption,description,story,source&access_token=$tk"));
                    }else {
                        $stream = (file_get_contents("https://graph.facebook.com/" . $page->getName() . "/feed?fields=id,created_time,link,type,message,full_picture,object_id,picture,name,caption,description,story,source&access_token=$tk"));
                    }
                }
            }
        }
        foreach ($this->clearStrings as $str) {
            while(strpos($stream, $str) !== false){
                $pos = strpos($stream, $str);
                $stream = substr_replace($stream,'',$pos,12);
            }
        }
        $stream = json_decode($stream);

        foreach($stream->data as $entry) {
            $post = $this->postRepository->findHiddenById($entry->id,$page->getUid());
            if ($post->toArray()) {
                $postalready = 1;
                $post = $post[0];
            } else {
                $post = new \Socialstream\SocialStream\Domain\Model\Post();
                $postalready = 0;
            }
            $post->setId($entry->id);
            $post->setObjectId($entry->object_id);
            $post->setCreatedTime(new \DateTime($entry->created_time));
            $post->setLink($entry->link);
            if ($entry->type == "link") {
                $post->setType(1);
            } elseif ($entry->type == "photo") {
                $post->setType(2);
            } elseif ($entry->type == "event") {
                $post->setType(3);
            } elseif ($entry->type == "video") {
                $post->setType(4);
            } elseif ($entry->type == "status") {
                $post->setType(5);
            } elseif ($entry->type == "offer") {
                $post->setType(6);
            } else {
                $post->setType(0);
            }
            if($entry->name)$post->setName($entry->name);
            if($entry->caption)$post->setCaption($entry->caption);
            if($entry->description)$post->setDescription($entry->description);
            if($entry->message) {
                $message = str_replace("\n", "<br/>", $entry->message);
                $post->setMessage(str_replace("<br/><br/>", "<br/>", $message));
            }
            if($entry->story)$post->setStory($entry->story);
            if($entry->full_picture)$post->setPictureUrl($entry->full_picture);
            if($entry->source)$post->setVideoUrl($entry->source);

            $post->setPage($page);

            if ($postalready) {
                $this->postRepository->update($post);
            } else {
                $this->postRepository->add($post);
                $clear = 1;
            }
            $persistenceManager->persistAll();

            $bild = NULL;
            $bildurl = $post->getPictureUrl();
            $bildname = $entry->id . ".jpg";

            if(!$postsFolder->hasFile($bildname) && $bildname) {
                if ($this->exists($bildurl)) {
                    $this->grab_image($bildurl,$this->tmp . $bildname);
                    $movedNewFile = $storage->addFile($this->tmp . $bildname, $postsFolder, $bildname);
                    $bild = $movedNewFile->getUid();
                }
                if ($post->getPicture()) {
                    $GLOBALS["TYPO3_DB"]->exec_UPDATEquery("sys_file_reference", "uid=" . $post->getPicture()->getUid(), array('deleted' => '1'));
                }
            } elseif (!$post->getPicture()  && $bildname) {
                $bild = $storage->getFile("/" . $targetFolder->getName() . "/" . $subFolder->getName() . "/" . $postsFolder->getName() . "/" . $bildname);
                $bild = $bild->getUid();
            }
            if ($bild) {
                $data = array();
                $data['sys_file_reference']['NEW12345'] = array(
                    'uid_local' => $bild,
                    'uid_foreign' => $post->getUid(), // uid of your content record
                    'tablenames' => 'tx_socialstream_domain_model_post',
                    'fieldname' => 'picture',
                    'pid' => $this->storagePid, // parent id of the parent page
                    'table_local' => 'sys_file',
                );
                $data['tx_socialstream_domain_model_post'][$post->getUid()] = array('picture' => 'NEW12345'); // set to the number of images?

                /** @var \TYPO3\CMS\Core\DataHandling\DataHandler $tce */
                $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler'); // create TCE instance
                $tce->bypassAccessCheckForRecords = TRUE;
                $tce->start($data, array());
                $tce->admin = TRUE;
                $tce->process_datamap();
                $clear = 1;
            }
            $bild = NULL;
            $bildurl = $post->getVideoUrl();
            $bildname = $entry->id . ".mp4";

            if(!$postsFolder->hasFile($bildname) && $bildname) {
                if ($this->exists($bildurl)) {
                    $this->grab_image($bildurl,$this->tmp . $bildname);
                    $movedNewFile = $storage->addFile($this->tmp . $bildname, $postsFolder, $bildname);
                    $bild = $movedNewFile->getUid();
                }
                if ($post->getVideo()) {
                    $GLOBALS["TYPO3_DB"]->exec_UPDATEquery("sys_file_reference", "uid=" . $post->getVideo()->getUid(), array('deleted' => '1'));
                }
            } elseif (!$post->getVideo()  && $bildname) {
                $bild = $storage->getFile("/" . $targetFolder->getName() . "/" . $subFolder->getName() . "/" . $postsFolder->getName() . "/" . $bildname);
                $bild = $bild->getUid();
            }
            if ($bild) {
                $data = array();
                $data['sys_file_reference']['NEW12345'] = array(
                    'uid_local' => $bild,
                    'uid_foreign' => $post->getUid(), // uid of your content record
                    'tablenames' => 'tx_socialstream_domain_model_post',
                    'fieldname' => 'video',
                    'pid' => $this->storagePid, // parent id of the parent page
                    'table_local' => 'sys_file',
                );
                $data['tx_socialstream_domain_model_post'][$post->getUid()] = array('video' => 'NEW12345'); // set to the number of images?

                /** @var \TYPO3\CMS\Core\DataHandling\DataHandler $tce */
                $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler'); // create TCE instance
                $tce->bypassAccessCheckForRecords = TRUE;
                $tce->start($data, array());
                $tce->admin = TRUE;
                $tce->process_datamap();
                $clear = 1;
            }
        }


        if($stream->paging->next && !$short){
            $clear += $this->postProcess($page,$storage,$targetFolder,$subFolder,$postsFolder,$short,$stream->paging->next);
        }
        return $clear;
    }
    private function galleryProcess($page,$storage,$targetFolder,$subFolder,$galleryFolder,$short,$paging=""){
        $persistenceManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');

        if($paging){
            $stream = (file_get_contents($paging));
        }else {
            $tk = $page->getToken();
            if(!$tk){
                $tk = $this->fbappid."|".$this->fbappsecret;
            }
            if ($short) {
                if($page->getMe()){
                    $stream = (file_get_contents("https://graph.facebook.com/me/photos/uploaded?fields=source,created_time,id&access_token=$tk&limit=$this->limitGalleries"));
                }else {
                    if ($page->getId()) {
                        $stream = (file_get_contents("https://graph.facebook.com/" . $page->getId() . "/photos/uploaded?fields=source,created_time,id&access_token=$tk&limit=$this->limitGalleries"));
                    } else {
                        $stream = (file_get_contents("https://graph.facebook.com/" . $page->getName() . "/photos/uploaded?fields=source,created_time,id&access_token=$tk&limit=$this->limitGalleries"));
                    }
                }
            } else {
                if($page->getMe()){
                    $stream = (file_get_contents("https://graph.facebook.com/me/photos/uploaded?fields=source,created_time,id&access_token=$tk"));
                }else {
                    if ($page->getId()) {
                        $stream = (file_get_contents("https://graph.facebook.com/" . $page->getId() . "/photos/uploaded?fields=source,created_time,id&access_token=$tk"));
                    } else {
                        $stream = (file_get_contents("https://graph.facebook.com/" . $page->getName() . "/photos/uploaded?fields=source,created_time,id&access_token=$tk"));
                    }
                }
            }
        }
        foreach ($this->clearStrings as $str) {
            while(strpos($stream, $str) !== false){
                $pos = strpos($stream, $str);
                $stream = substr_replace($stream,'',$pos,12);
            }
        }
        $stream = json_decode($stream);

        foreach($stream->data as $entry) {
            $gallery = $this->galleryRepository->findHiddenById($entry->id,$page->getUid());
            if ($gallery->toArray()) {
                $galleryalready = 1;
                $gallery = $gallery[0];
            } else {
                $gallery = new \Socialstream\SocialStream\Domain\Model\Gallery();
                $galleryalready = 0;
            }
            $gallery->setId($entry->id);
            $gallery->setPictureUrl($entry->source);
            $gallery->setCreatedTime(new \DateTime($entry->created_time));

            $gallery->setPage($page);

            if($galleryalready) {
                $this->galleryRepository->update($gallery);
            }else{
                $this->galleryRepository->add($gallery);
                $clear = 1;
            }
            $persistenceManager->persistAll();

            $bild = NULL;
            $bildurl = $gallery->getPictureUrl();
            $bildname = $entry->id . ".jpg";

            if(!$galleryFolder->hasFile($bildname) && $bildname) {
                if ($this->exists($bildurl)) {
                    $this->grab_image($bildurl,$this->tmp . $bildname);
                    $movedNewFile = $storage->addFile($this->tmp . $bildname, $galleryFolder, $bildname);
                    $bild = $movedNewFile->getUid();
                }
                if ($gallery->getPicture()) {
                    $GLOBALS["TYPO3_DB"]->exec_UPDATEquery("sys_file_reference", "uid=" . $gallery->getPicture()->getUid(), array('deleted' => '1'));
                }
            }elseif(!$gallery->getPicture()  && $bildname){
                $bild = $storage->getFile("/".$targetFolder->getName()."/".$subFolder->getName()."/".$galleryFolder->getName()."/".$bildname);
                $bild = $bild->getUid();
            }
            if ($bild) {
                $data = array();
                $data['sys_file_reference']['NEW123456'] = array(
                    'uid_local' => $bild,
                    'uid_foreign' => $gallery->getUid(), // uid of your content record
                    'tablenames' => '	tx_socialstream_domain_model_gallery',
                    'fieldname' => 'picture',
                    'pid' => $this->storagePid, // parent id of the parent page
                    'table_local' => 'sys_file',
                );
                $data['tx_socialstream_domain_model_gallery'][$gallery->getUid()] = array('picture' => 'NEW123456'); // set to the number of images?

                /** @var \TYPO3\CMS\Core\DataHandling\DataHandler $tce */
                $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler'); // create TCE instance
                $tce->bypassAccessCheckForRecords = TRUE;
                $tce->start($data, array());
                $tce->admin = TRUE;
                $tce->process_datamap();
                $clear = 1;
            }

        }
        if($stream->paging->next && !$short){
            $clear += $this->galleryProcess($page,$storage,$targetFolder,$subFolder,$galleryFolder,$short,$stream->paging->next);
        }
        return $clear;
    }

    private function eventProcess($page,$storage,$targetFolder,$subFolder,$eventFolder,$short,$paging=""){
        $persistenceManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
        
        if($paging){
            $stream = (file_get_contents($paging));
        }else {
            $tk = $page->getToken();
            if(!$tk){
                $tk = $this->fbappid."|".$this->fbappsecret;
            }
            if ($short) {
                if ($page->getId()) {
                    $stream = (file_get_contents("https://graph.facebook.com/" . $page->getId() . "/events?fields=id,start_time,end_time,name,description,place,cover&access_token=$tk&limit=$this->limitGalleries"));
                } else {
                    $stream = (file_get_contents("https://graph.facebook.com/" . $page->getName() . "/events?fields=id,start_time,end_time,name,description,place,cover&access_token=$tk&limit=$this->limitGalleries"));
                }
            } else {
                if ($page->getId()) {
                    $stream = (file_get_contents("https://graph.facebook.com/" . $page->getId() . "/events?fields=id,start_time,end_time,name,description,place,cover&access_token=$tk"));
                } else {
                    $stream = (file_get_contents("https://graph.facebook.com/" . $page->getName() . "/events?fields=id,start_time,end_time,name,description,place,cover&access_token=$tk"));
                }
            }
        }
        foreach ($this->clearStrings as $str) {
            while(strpos($stream, $str) !== false){
                $pos = strpos($stream, $str);
                $stream = substr_replace($stream,'',$pos,12);
            }
        }
        $stream = json_decode($stream);

        foreach($stream->data as $entry) {
            $event = $this->eventRepository->findHiddenById($entry->id);
            if ($event->toArray()) {
                $eventalready = 1;
                $event = $event[0];
            } else {
                $event = new \Socialstream\SocialStream\Domain\Model\Event();
                $eventalready = 0;
            }
            $event->setId($entry->id);
            $event->setStartTime(new \DateTime($entry->start_time));
            $event->setEndTime(new \DateTime($entry->end_time));
            if($entry->name)$event->setName($entry->name);
            if($entry->description) {
                $message = str_replace("\n", "<br/>", $entry->description);
                $event->setDescription(str_replace("<br/><br/>", "<br/>", $message));
            }
            if($entry->place->name)$event->setPlaceName($entry->place->name);
            if($entry->place->location->street)$event->setPlaceStreet($entry->place->location->street);
            if($entry->place->location->zip)$event->setPlaceZip($entry->place->location->zip);
            if($entry->place->location->city)$event->setPlaceCity($entry->place->location->city);
            if($entry->place->location->country)$event->setPlaceCountry($entry->place->location->country);
            if($entry->place->location->latitude)$event->setLat($entry->place->location->latitude);
            if($entry->place->location->longitude)$event->setLng($entry->place->location->longitude);
            if($entry->cover->source)$event->setPictureUrl($entry->cover->source);
            $event->setPage($page);

            if($eventalready) {
                $this->eventRepository->update($event);
            }else{
                $this->eventRepository->add($event);
                $clear = 1;
            }
            $persistenceManager->persistAll();

            $bild = NULL;
            $bildurl = $event->getPictureUrl();
            $bildname = $entry->id . ".jpg";

            if(!$eventFolder->hasFile($bildname) && $bildname) {
                if ($this->exists($bildurl)) {
                    $this->grab_image($bildurl,'/tmp/' . $bildname);
                    $movedNewFile = $storage->addFile('/tmp/' . $bildname, $eventFolder, $bildname);
                    $bild = $movedNewFile->getUid();
                }
                if ($event->getPicture()) {
                    $GLOBALS["TYPO3_DB"]->exec_UPDATEquery("sys_file_reference", "uid=" . $event->getPicture()->getUid(), array('deleted' => '1'));
                }
            }elseif(!$event->getPicture()  && $bildname){
                $bild = $storage->getFile("/".$targetFolder->getName()."/".$subFolder->getName()."/".$eventFolder->getName()."/".$bildname);
                $bild = $bild->getUid();
            }
            if ($bild) {
                $data = array();
                $data['sys_file_reference']['NEW123456'] = array(
                    'uid_local' => $bild,
                    'uid_foreign' => $event->getUid(), // uid of your content record
                    'tablenames' => '	tx_socialstream_domain_model_event',
                    'fieldname' => 'picture',
                    'pid' => $this->storagePid, // parent id of the parent page
                    'table_local' => 'sys_file',
                );
                $data['tx_socialstream_domain_model_event'][$event->getUid()] = array('picture' => 'NEW123456'); // set to the number of images?

                /** @var \TYPO3\CMS\Core\DataHandling\DataHandler $tce */
                $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler'); // create TCE instance
                $tce->bypassAccessCheckForRecords = TRUE;
                $tce->start($data, array());
                $tce->admin = TRUE;
                $tce->process_datamap();
                $clear = 1;
            }

        }
        if($stream->paging->next && !$short){
            $clear += $this->eventProcess($page,$storage,$targetFolder,$subFolder,$eventFolder,$short,$stream->paging->next);
        }
        return $clear;
    }

    protected function grab_image($url,$saveto){
        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $raw=curl_exec($ch);
        curl_close ($ch);
        if(file_exists($saveto)){
            unlink($saveto);
        }
        $fp = fopen($saveto,'x');
        fwrite($fp, $raw);
        fclose($fp);
    }

    protected function initTSFE($id = 1, $typeNum = 0) {
        \TYPO3\CMS\Frontend\Utility\EidUtility::initTCA();
        if (!is_object($GLOBALS['TT'])) {
            $GLOBALS['TT'] = new \TYPO3\CMS\Core\TimeTracker\NullTimeTracker;
            $GLOBALS['TT']->start();
        }
        $GLOBALS['TSFE'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController',
            $GLOBALS['TYPO3_CONF_VARS'], $id, $typeNum);
        $GLOBALS['TSFE']->connectToDB();
        $GLOBALS['TSFE']->initFEuser();
        $GLOBALS['TSFE']->determineId();
        $GLOBALS['TSFE']->initTemplate();
        $GLOBALS['TSFE']->getConfigArray();

    }
    protected function exists($path){
        return (@fopen($path,"r")==true);
    }

    protected function header_req( $url )
    {
        $channel = curl_init();
        curl_setopt($channel, CURLOPT_URL, $url);
        curl_setopt($channel, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($channel, CURLOPT_TIMEOUT, 10);
        curl_setopt($channel, CURLOPT_HEADER, true);
        curl_setopt($channel, CURLOPT_NOBODY, true);
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($channel, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201');
        curl_setopt($channel, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($channel, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($channel, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_exec($channel);
        $httpCode = curl_getinfo( $channel, CURLINFO_HTTP_CODE );
        curl_close($channel);
        return $httpCode;
    }

}