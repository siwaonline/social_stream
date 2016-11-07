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
 * LinkedInController
 */
class LinkedInController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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

    protected $liappid = "";
    protected $liappsecret = "";
    protected $limitPosts = "";
    protected $limitGalleries = "";
    protected $maxText = 0;
    protected $maxWidth = 0;
    protected $maxHeight = 0;
    protected $storagePid = 0;
    protected $tmp = "/tmp/";
    protected $sysmail = "";

    public $rootPage = 1;
    
    protected $streamtype = 6;

    public function initializeAction(){
        $this->liappid = $this->settings['liappid'];
        $this->liappsecret = $this->settings['liappsecret'];
        $this->limitPosts = $this->settings['limitPosts'];
        $this->limitGalleries = $this->settings['limitGalleries'];
        $this->maxText = $this->settings['maxText'];
        $this->maxWidth = $this->settings['maxWidth'];
        $this->maxHeight = $this->settings['maxHeight'];
        $this->storagePid = $this->settings['storagePid'];
        $this->tmp = $this->settings['tmp'];
        $this->sysmail = $this->settings['sysmail'];
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
        $pagenameRefresh = $vars["pageRefresh"];

        if($pagename) {
            if ($pagename != "me") {
                $dbpage = $this->pageRepository->searchById($pagename,$this->streamtype);
                if ($dbpage) {
                    $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.already', 'social_stream');
                    $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error', 'social_stream');
                    $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg));
                }else{
                    $headers = get_headers("https://www.linkedin.com/company/".$pagename);
                    $resp = substr($headers[0], 9, 3);
                    if($resp != 200 && $resp != 302 && $resp != 999){
                        $msg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.nopage', 'social_stream');
                        $head = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('msg.error', 'social_stream');
                        $this->redirect('message', 'Page', null, array('head' => $head, 'message' => $msg));
                    }
                }
            }
        }else{
            $pagename = $vars["pageRefresh"];
        }

        if(!$_GET["name"]) {
            $this->uriBuilder->reset();
            $this->uriBuilder->setArguments(array(
                'tx_socialstream_web_socialstreambe1' => array(
                    'action' => 'token',
                    'controller' => 'LinkedIn'
                ),
                'name' => $pagename
            ));
            $this->uriBuilder->setCreateAbsoluteUri(1);
            $url = urlencode($this->uriBuilder->buildBackendUri());
            $accessurl = "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=" . $this->liappid . "&state=" . time() . "&scope=r_basicprofile%20rw_company_admin&redirect_uri=".$url;
            $this->view->assign('accessurl', $accessurl);
        }else{
            $code = $_GET["code"];
            $state = $_GET["state"];
            $this->uriBuilder->reset();
            $this->uriBuilder->setArguments(array(
                'tx_socialstream_web_socialstreambe1' => array(
                    'action' => 'token',
                    'controller' => 'LinkedIn'
                ),
                'name' => $_GET["name"]
            ));
            $this->uriBuilder->setCreateAbsoluteUri(1);
            $url = urlencode($this->uriBuilder->buildBackendUri());
            $accessurl = "https://www.linkedin.com/oauth/v2/accessToken?grant_type=authorization_code&code=" . $code . "&client_id=" . $this->liappid . "&client_secret=" . $this->liappsecret . "&redirect_uri=";
            $resp = json_decode(file_get_contents($accessurl.$url));
            
            $page = $this->pageRepository->searchById($_GET["name"],$this->streamtype);
            if(!$page){
                $page = new \Socialstream\SocialStream\Domain\Model\Page();
                $page->setName($_GET["name"]);
                $page->setStreamtype($this->streamtype);
            }
            $page->setToken($resp->access_token);
            $page->setExpires(time()+$resp->expires_in/1000);

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
            if($storage->hasFolder("linkedin")){
                $targetFolder = $storage->getFolder('linkedin');
            }else{
                $targetFolder = $storage->createFolder('linkedin');
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
     * action getLinkedIn
     *
     * @return void
     */
    public function getLinkedInAction()
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
        $pages = $this->pageRepository->findByStreamtype(6);
        $clear = 0;

        foreach($pages as $page){
            $storageRepository = $this->objectManager->get('TYPO3\\CMS\\Core\\Resource\\StorageRepository');
            $storage = $storageRepository->findByUid('1');
            if($storage->hasFolder("linkedin")){
                $targetFolder = $storage->getFolder('linkedin');
            }else{
                $targetFolder = $storage->createFolder('linkedin');
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

        if(!$this->liappid || !$this->liappsecret){
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
        if($expdiff <= 5 && $tk && $this->sysmail && $showerror == 0){
            $this->uriBuilder->reset();
            $this->uriBuilder->setCreateAbsoluteUri(1);
            $url = explode("?",$this->uriBuilder->buildBackendUri())[0];
            
            $subject = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('mail.subject_token', 'social_stream');
            $text = "Social Stream LinkedIn ".$page->getName().": <br/>";
            $text .= \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('mail.body_token', 'social_stream');
            $text .= "<br/><br/>" . $url;
            $message = (new \TYPO3\CMS\Core\Mail\MailMessage())
                ->setFrom(array("noreply@siwa.at" => "Social Stream"))
                ->setTo(array($this->sysmail => $this->sysmail))
                ->setSubject("=?utf-8?b?".base64_encode("LinkedIn ".$page->getName().": ".$subject)."?=")
                ->setBody($text,"text/html");
            $message->send();
        }

        if ($page->getName() == "me"){
            $page->setMe(1);
        }
        try {
            if ($page->getMe()) {
                $elem = (file_get_contents("https://api.linkedin.com/v1/people/~:(id,first-name,last-name,headline,picture-url,picture-urls::(original),publicProfileUrl,industry)?format=json&oauth2_access_token=$tk"));
            } else {
                if($page->getId()) {
                    $elem = (file_get_contents("https://api.linkedin.com/v1/companies/" . $page->getId() . ":(id,name,description,website-url,specialties,industry,logo-url)?format=json&oauth2_access_token=$tk"));
                }else{
                    $elem = (file_get_contents("https://api.linkedin.com/v1/companies/" . $page->getName() . ":(id,name,description,website-url,specialties,industry,logo-url)?format=json&oauth2_access_token=$tk"));
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
        while(strpos($elem, '\ud83d\u') !== false){
            $pos = strpos($elem, '\ud83d\u');
            $stream = substr_replace($elem,'',$pos,12);
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
        if($page->getMe()) {
            $page->setName($elem->firstName." ".$elem->lastName);
        }else{
            $page->setName($elem->name);
        }
        if($elem->industry)$page->setAbout($elem->industry);
        if($page->getMe()) {
            $page->setDescription($elem->headline);
        }else{
            $page->setDescription($elem->description);
        }
        if($page->getMe()) {
            $page->setLink($elem->publicProfileUrl);
        }else{
            $page->setLink($elem->websiteUrl);
        }
        $page->setStreamtype($this->streamtype);
        if($page->getMe()) {
            $bildurl = $elem->pictureUrls->values[0];
            if(!$bildurl)$bildurl = $elem->pictureUrl;
        }else{
            $bildurl = $elem->logoUrl;
        }
        $page->setPictureUrl($bildurl);
        $bildname = explode("?",basename($bildurl));
        if (is_array($bildname)){
            $bildname = $bildname[0];
        }

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
                if ($page->getMe()) {
                    /* TODO */
                } else {
                    if($page->getId()){
                        $stream = (file_get_contents("https://api.linkedin.com/v1/companies/" . $page->getId() . "/updates/?format=json&oauth2_access_token=$tk&count=$this->limitPosts"));
                    }else {
                        $stream = (file_get_contents("https://api.linkedin.com/v1/companies/" . $page->getName() . "/updates/?format=json&oauth2_access_token=$tk&count=$this->limitPosts"));
                    }
                }
            } else {
                if ($page->getMe()) {
                    /* TODO */
                } else {
                    if($page->getId()) {
                        $elem = (file_get_contents("https://api.linkedin.com/v1/companies/" . $page->getId() . "/updates/?format=json&oauth2_access_token=$tk"));
                    }else{
                        $elem = (file_get_contents("https://api.linkedin.com/v1/companies/" . $page->getName() . "/updates/?format=json&oauth2_access_token=$tk"));
                    }
                }
            }
        }
        while(strpos($stream, '\ud83d\u') !== false){
            $pos = strpos($stream, '\ud83d\u');
            $stream = substr_replace($stream,'',$pos,12);
        }
        $stream = json_decode($stream);

        foreach($stream->values as $val) {
            $entry = $val->updateContent->companyStatusUpdate->share;
            
            $post = $this->postRepository->findHiddenById($entry->id,$page->getUid());
            if ($post->toArray()) {
                $postalready = 1;
                $post = $post[0];
            } else {
                $post = new \Socialstream\SocialStream\Domain\Model\Post();
                $postalready = 0;
            }
            $post->setId($entry->id);
            $date = new \DateTime();
            $date->setTimestamp($entry->timestamp/1000);
            $post->setCreatedTime($date);
            $post->setLink($entry->content->shortenedUrl);
            if($entry->content->title)$post->setName($entry->content->title);
            if($entry->content->description)$post->setCaption($entry->content->description);
            if($entry->comment) {
                $message = str_replace("\n", "<br/>", $entry->comment);
                $post->setMessage(str_replace("<br/><br/>", "<br/>", $message));
            }
            if($entry->content->submittedImageUrl)$post->setPictureUrl($entry->content->submittedImageUrl);

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

            if((!$postsFolder->hasFile($bildname) && $bildname) || ($storage->getFileInFolder($bildname,$postsFolder)->getSize() <= 0 && $postsFolder->hasFile($bildname) && $bildname)) {
                if ($this->exists($bildurl)) {
                    $this->grab_image($bildurl,$this->tmp . $bildname);
                    $movedNewFile = $storage->addFile($this->tmp . $bildname, $postsFolder, $bildname,  \TYPO3\CMS\Core\Resource\DuplicationBehavior::REPLACE);
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