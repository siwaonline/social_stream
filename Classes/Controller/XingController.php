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
 * XingController
 */
class XingController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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

    protected $xingappid = "";
    protected $xingappsecret = "";
    protected $limitPosts = "";
    protected $limitGalleries = "";
    protected $maxText = 0;
    protected $maxWidth = 0;
    protected $maxHeight = 0;
    protected $storagePid = 0;
    protected $tmp = "/tmp/";

    public $rootPage = 1;

    protected $streamtype = 5;

    public function initializeAction(){
        $this->xingappid = $this->settings['xingappid'];
        $this->xingappsecret = $this->settings['xingappsecret'];
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
            $dbpage = $this->pageRepository->searchByName($pagename,$this->streamtype);
            if ($dbpage->toArray()) {
                $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.already', 'social_stream');
                $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error', 'social_stream');
                $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg));
            }/*else{
                $headers = get_headers("https://www.xing.com/companies/".$pagename);
                $resp = substr($headers[0], 9, 3);
                if($resp != 200 && $resp != 302){
                    $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.nopage', 'social_stream');
                    $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error', 'social_stream');
                    $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg));
                }
            }*/
        }

        if(!$_GET["name"]) {
            $this->uriBuilder->reset();
            //$this->uriBuilder->uriFor('token', array(), "Xing", "social_stream", "be1");
            $this->uriBuilder->setArguments(array(
                'tx_socialstream_web_socialstreambe1' => array(
                    'action' => 'token',
                    'controller' => 'Xing'
                ),
                'name' => $pagename
            ));
            $this->uriBuilder->setCreateAbsoluteUri(1);
            $url = urlencode($this->uriBuilder->buildBackendUri());
            $accessurl = "https://api.xing.com/v1/request_token?oauth_consumer_key=" . $this->xingappid . "&oauth_signature_method=PLAINTEXT&oauth_signature=" . $this->xingappsecret . "%26&oauth_nonce=" . time() . "&oauth_timestamp=" . time() . "&oauth_callback=";
            $resp = file_get_contents($accessurl.$url);
            $token = explode("=",explode("&",$resp)[0])[1];
            $secret = explode("=",explode("&",$resp)[1])[1];
            setcookie("secret", $secret);
            $accessurl = "https://api.xing.com/v1/authorize?oauth_consumer_key=" . $this->xingappid . "&oauth_signature_method=PLAINTEXT&oauth_signature=" . $secret . "%26&oauth_token=" . $token . "&oauth_nonce=" . time() . "&oauth_timestamp=" . time() . "&oauth_callback=";
            $this->view->assign('accessurl', $accessurl);
            $this->view->assign('name', $pagename);
        }else{
            $accessurl = "https://api.xing.com/v1/access_token?oauth_consumer_key=" . $this->xingappid . "&oauth_signature_method=PLAINTEXT&oauth_signature=" . $this->xingappsecret ."%26". $_COOKIE["secret"] . "&oauth_token=" . $_GET["oauth_token"] . "&oauth_verifier=" . $_GET["oauth_verifier"] . "&oauth_nonce=" . time() . "&oauth_timestamp=" . time() . "&oauth_callback=";
            //$resp = file_get_contents($accessurl."oob");
            $this->uriBuilder->reset();
            //$this->uriBuilder->uriFor('token', array(), "Xing", "social_stream", "be1");
            $this->uriBuilder->setArguments(array(
                'tx_socialstream_web_socialstreambe1' => array(
                    'action' => 'token',
                    'controller' => 'Xing'
                ),
                'name' => $_GET["name"]
            ));
            $this->uriBuilder->setCreateAbsoluteUri(1);
            $url = urlencode($this->uriBuilder->buildBackendUri());
            $resp = file_get_contents($accessurl.$url);
            $token = explode("=",explode("&",$resp)[0])[1];
            $secret = explode("=",explode("&",$resp)[1])[1];
            $user = explode("=",explode("&",$resp)[2])[1];

            $page = new \Socialstream\SocialStream\Domain\Model\Page();
            $page->setName($_GET["name"]);
            $page->setStreamtype($this->streamtype);
            $page->setToken($token);
            $page->setTokensecret($this->xingappsecret . "%26" . $secret);

            $this->forward('create', null, null, array('page' => $page, 'short' => 1, 'close' => 1));
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
                $dbpage = $this->pageRepository->searchById($page->getName(),$this->streamtype);
                if ($dbpage) {
                    $already = 1;
                    $page = $dbpage;
                }
            }

            $storageRepository = $this->objectManager->get('TYPO3\\CMS\\Core\\Resource\\StorageRepository');
            $clear = 0;
            $storage = $storageRepository->findByUid('1');
            if($storage->hasFolder("xing")){
                $targetFolder = $storage->getFolder('xing');
            }else{
                $targetFolder = $storage->createFolder('xing');
            }

            try {
                // ### get Page Data ###
                $page = $this->pageProcess($page,$storage,$targetFolder,$already);
                if(is_array($page)){
                    foreach ($page as $p){
                        if ($targetFolder->hasFolder($p->getId())) {
                            $subFolder = $targetFolder->getSubfolder($p->getId());
                        } else {
                            $subFolder = $targetFolder->createFolder($p->getId());
                        }

                        // ### get Posts of Page ###                
                        if($subFolder->hasFolder("posts")){
                            $postsFolder = $subFolder->getSubfolder("posts");
                        }else{
                            $postsFolder = $subFolder->createFolder("posts");
                        }
                        $clear += $this->postProcess($p,$storage,$targetFolder,$subFolder,$postsFolder,$short);
                    }
                }else {
                    if ($targetFolder->hasFolder($page->getId())) {
                        $subFolder = $targetFolder->getSubfolder($page->getId());
                    } else {
                        $subFolder = $targetFolder->createFolder($page->getId());
                    }

                    // ### get Posts of Page ###                
                    if($subFolder->hasFolder("posts")){
                        $postsFolder = $subFolder->getSubfolder("posts");
                    }else{
                        $postsFolder = $subFolder->createFolder("posts");
                    }
                    $clear += $this->postProcess($page,$storage,$targetFolder,$subFolder,$postsFolder,$short);
                }

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
     * action getXing
     *
     * @return void
     */
    public function getXingAction()
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
        $pages = $this->pageRepository->findByStreamtype(5);
        $clear = 0;

        foreach($pages as $page){
            $storageRepository = $this->objectManager->get('TYPO3\\CMS\\Core\\Resource\\StorageRepository');
            $storage = $storageRepository->findByUid('1');
            if($storage->hasFolder("xing")){
                $targetFolder = $storage->getFolder('xing');
            }else{
                $targetFolder = $storage->createFolder('xing');
            }

            try {
                // ### get Page Data ###
                $page = $this->pageProcess($page,$storage,$targetFolder,1);
                if(is_array($page)){
                    foreach ($page as $p){
                        if ($targetFolder->hasFolder($p->getId())) {
                            $subFolder = $targetFolder->getSubfolder($p->getId());
                        } else {
                            $subFolder = $targetFolder->createFolder($p->getId());
                        }

                        // ### get Posts of Page ###                
                        if($subFolder->hasFolder("posts")){
                            $postsFolder = $subFolder->getSubfolder("posts");
                        }else{
                            $postsFolder = $subFolder->createFolder("posts");
                        }
                        $clear += $this->postProcess($p,$storage,$targetFolder,$subFolder,$postsFolder,$short);
                    }
                }else {
                    if ($targetFolder->hasFolder($page->getId())) {
                        $subFolder = $targetFolder->getSubfolder($page->getId());
                    } else {
                        $subFolder = $targetFolder->createFolder($page->getId());
                    }

                    // ### get Posts of Page ###                
                    if($subFolder->hasFolder("posts")){
                        $postsFolder = $subFolder->getSubfolder("posts");
                    }else{
                        $postsFolder = $subFolder->createFolder("posts");
                    }
                    $clear += $this->postProcess($page,$storage,$targetFolder,$subFolder,$postsFolder,$short);
                }                
                

            } catch (\TYPO3\CMS\Core\Error\Exception $e) {
                echo "".$e->getMessage();
            }
        }
        if($clear > 0){
            $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler');
            $tce->clear_cacheCmd('cacheTag:socialStream');
        }

    }

    private function pageProcess($page,$storage,$targetFolder,$already,$rec=0){
        $persistenceManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
        if(!$this->xingappid || !$this->xingappsecret){
            $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.noapp', 'social_stream');
            $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error', 'social_stream');
            $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg, 'close' => 1));
        }
        if(!$this->storagePid){
            $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.nostorage', 'social_stream');
            $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error', 'social_stream');
            $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg, 'close' => 1));
        }

        $tk = $page->getToken();
        if ($page->getName() == "me"){
            $page->setMe(1);
        }
        try {
            if ($page->getMe()) {
                if($page->getId()) {
                    $elem = (file_get_contents("https://api.xing.com/v1/users/" . $page->getId() . ".json?oauth_consumer_key=" . $this->xingappid . "&oauth_token=" . $page->getToken() . "&oauth_signature_method=PLAINTEXT&oauth_timestamp=" . time() . "&oauth_nonce=" . time() . "&oauth_version=1.0&oauth_signature=" . $page->getTokensecret()));
                }else{
                    $elem = (file_get_contents("https://api.xing.com/v1/users/" . $page->getName() . ".json?oauth_consumer_key=" . $this->xingappid . "&oauth_token=" . $page->getToken() . "&oauth_signature_method=PLAINTEXT&oauth_timestamp=" . time() . "&oauth_nonce=" . time() . "&oauth_version=1.0&oauth_signature=" . $page->getTokensecret()));
                }
            } else {
                if($page->getId()) {
                    $elem = (file_get_contents("https://api.xing.com/v1/companies/" . $page->getId() . ".json?oauth_consumer_key=" . $this->xingappid . "&oauth_token=" . $page->getToken() . "&oauth_signature_method=PLAINTEXT&oauth_timestamp=" . time() . "&oauth_nonce=" . time() . "&oauth_version=1.0&oauth_signature=" . $page->getTokensecret()));
                }else{
                    $elem = (file_get_contents("https://api.xing.com/v1/users/me/companies/managing.json?oauth_consumer_key=" . $this->xingappid . "&oauth_token=" . $page->getToken() . "&oauth_signature_method=PLAINTEXT&oauth_timestamp=" . time() . "&oauth_nonce=" . time() . "&oauth_version=1.0&oauth_signature=" . $page->getTokensecret()));
                    if(!$elem){
                        $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.nodata', 'social_stream');
                        $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error', 'social_stream');
                        $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg, 'close' => 1));
                    }
                    $elem = json_decode($elem);
                    $pages = array();
                    foreach($elem->companies->items as $entry) {
                        $temppage = $page;
                        $temppage->setId($entry->id);
                        $temppage = $this->pageProcess($temppage,$storage,$targetFolder,$already);
                        array_push($pages,$temppage);
                    }

                    return $pages;
                }
            }

        } catch (\TYPO3\CMS\Core\Error\Exception $e) {
            $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.nodata', 'social_stream');
            $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error', 'social_stream');
            $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg, 'close' => 1));
        }
        if(!$elem){
            $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.nodata', 'social_stream');
            $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error', 'social_stream');
            $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg, 'close' => 1));
        }
        while(strpos($elem, '\ud83d\u') !== false){
            $pos = strpos($elem, '\ud83d\u');
            $stream = substr_replace($elem,'',$pos,12);
        }
        $elem = json_decode($elem);
        if($page->getMe()) {
            $elem = $elem->users[0];
        }else{
            $elem = $elem->company;
        }

        $helppage = $this->pageRepository->findOneById($elem->id);
        if($helppage){
            $helppage->setToken($page->getToken());
            $helppage->setTokensecret($page->getTokensecret());
            $page = $helppage;
            $already = 1;
        } 

        $page->setId($elem->id);
        if($elem->display_name) {
            $page->setName($elem->display_name);
        }else{
            $page->setName($elem->name);
        }
        if($elem->wants)$page->setAbout($elem->wants);
        if($elem->haves)$page->setDescription($elem->haves);
        if($elem->description)$page->setDescription($elem->description);
        $page->setLink($elem->permalink);
        $page->setStreamtype($this->streamtype);

        if($page->getMe()){
            $bildurl = $elem->photo_urls->size_original;
        }else {
            $bildurl = $elem->logo_urls->size_192x192;
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

        if((!$subFolder->hasFile($bildname) && $bildname) || ($storage->getFileInFolder($bildname,$subFolder)->getSize() <= 0 && $subFolder->hasFile($bildname) && $bildname)) {
            if ($bildurl) {
                copy($bildurl, $this->tmp . $bildname);
                $movedNewFile = $storage->addFile($this->tmp . $bildname, $subFolder, $bildname,  \TYPO3\CMS\Core\Resource\DuplicationBehavior::REPLACE);
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
        
        return $page;
    }

    private function postProcess($page,$storage,$targetFolder,$subFolder,$postsFolder,$short,$paging=""){
        $persistenceManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
        $clear = 0;

        if($paging){
            $stream = (file_get_contents($paging));
        }else {
            $tk = $page->getToken();
            if ($short) {
                $limit = date('d.m.Y', strtotime("-1 week"));
                if ($page->getMe()) {
                    /* TODO */
                } else {
                    if($page->getId()){
                        $stream = (file_get_contents("https://api.xing.com/v1/companies/" . $page->getId() . "/updates.json?oauth_consumer_key=" . $this->xingappid . "&oauth_token=" . $page->getToken() . "&oauth_signature_method=PLAINTEXT&oauth_timestamp=" . time() . "&oauth_nonce=" . time() . "&oauth_version=1.0&oauth_signature=" . $page->getTokensecret()."&since=$limit"));
                    }else {
                        $stream = (file_get_contents("https://api.xing.com/v1/companies/" . $page->getName() . "/updates.json?oauth_consumer_key=" . $this->xingappid . "&oauth_token=" . $page->getToken() . "&oauth_signature_method=PLAINTEXT&oauth_timestamp=" . time() . "&oauth_nonce=" . time() . "&oauth_version=1.0&oauth_signature=" . $page->getTokensecret()."&since=$limit"));
                    }
                }
            } else {
                if ($page->getMe()) {
                    /* TODO */
                } else {
                    if($page->getId()){
                        $stream = (file_get_contents("https://api.xing.com/v1/companies/" . $page->getId() . "/updates.json?oauth_consumer_key=" . $this->xingappid . "&oauth_token=" . $page->getToken() . "&oauth_signature_method=PLAINTEXT&oauth_timestamp=" . time() . "&oauth_nonce=" . time() . "&oauth_version=1.0&oauth_signature=" . $page->getTokensecret()));
                    }else {
                        $stream = (file_get_contents("https://api.xing.com/v1/companies/" . $page->getName() . "/updates.json?oauth_consumer_key=" . $this->xingappid . "&oauth_token=" . $page->getToken() . "&oauth_signature_method=PLAINTEXT&oauth_timestamp=" . time() . "&oauth_nonce=" . time() . "&oauth_version=1.0&oauth_signature=" . $page->getTokensecret()));
                    }
                }
            }
        }
        while(strpos($stream, '\ud83d\u') !== false){
            $pos = strpos($stream, '\ud83d\u');
            $stream = substr_replace($stream,'',$pos,12);
        }
        $stream = json_decode($stream);

        foreach($stream->updates->items as $entry) {
            $post = $this->postRepository->findHiddenById($entry->id,$page->getUid());
            if ($post->toArray()) {
                $postalready = 1;
                $post = $post[0];
            } else {
                $post = new \Socialstream\SocialStream\Domain\Model\Post();
                $postalready = 0;
            }
            $post->setId($entry->id);
            $post->setCreatedTime(new \DateTime($entry->created_at));
            if($entry->headline)$post->setName($entry->headline);
            if($entry->content) {
                $message = str_replace("\r\n", "<br/>", $entry->content);
                $message = str_replace("\n", "<br/>", $message);
                $post->setMessage(str_replace("<br/><br/>", "<br/>", $message));
            }
            $post->setPage($page);

            if ($postalready) {
                $this->postRepository->update($post);
            } else {
                $this->postRepository->add($post);
                $clear = 1;
            }
            $persistenceManager->persistAll();

        }


        if($stream->paging->next && !$short){
            $clear += $this->postProcess($page,$storage,$targetFolder,$subFolder,$postsFolder,$short,$stream->paging->next);
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
    

}