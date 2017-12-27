<?php
namespace Socialstream\SocialStream\Utility\Feed;


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

use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use \TYPO3\CMS\Core\Messaging\AbstractMessage;
use \TYPO3\CMS\Core\Messaging\FlashMessageService;
use \TYPO3\CMS\Core\Messaging\FlashMessage;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * InstagramUtility
 */
class InstagramUtility extends \Socialstream\SocialStream\Utility\Feed\FeedUtility
{

    /**
     * __construct
     */
    public function __construct($pid=0)
    {
        if($pid) {
            $this->initTSFE($pid, 0);
            $this->initSettings();
        }
    }

    public function getChannel(\Socialstream\SocialStream\Domain\Model\Channel $channel,$isProcessing=0)
    {
        $url = "https://api.instagram.com/v1/users/".$channel->getObjectId()."/?access_token=".$channel->getToken();
        if($this->get_http_response_code($url) == 200) {
            $elem = $this->getElems($url);



            $channel->setObjectId($elem->data->id);
            $channel->setTitle($elem->data->username);
            if ($elem->data->bio) $channel->setAbout($elem->data->bio);
            //if($elem->description)$channel->setDescription($elem->description);
            $channel->setLink("https://www.instagram.com/" . $elem->data->username . "/");

            if ($isProcessing == 0) {
                //$picStream = json_decode(file_get_contents("https://graph.facebook.com/" . $channel->getObjectId() . "/picture?redirect=0&width=900&access_token=" . $channel->getToken()));
                $imageUrl = $elem->data->profile_picture;
                if ($this->exists($imageUrl)) {
                    $this->processChannelMedia($channel, $imageUrl);
                }
            }
        }else{
            if ($isProcessing == 0) {
                if($this->settings["sysmail"]) {
                    $this->sendTokenInfoMail($channel,$this->settings["sysmail"]);
                }
            }else{
                $msg = "Fehler: Channel konnte nicht gecrawlt werden. Object Id oder Token falsch.";
                //$this->addFlashMessage($msg, '', AbstractMessage::ERROR);
                $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
                $this->addFlashMessage($msg, '', FlashMessage::ERROR,$this->objectManager->get(FlashMessageService::class));
                return false;
            }
        }

        return $channel;
    }

    public function renewToken(\Socialstream\SocialStream\Domain\Model\Channel $channel){
        /*$expdiff = ($channel->getExpires() - time())/86400;
        if($expdiff <= 5){
            $url = "https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id=" . $this->settings["fbappid"] . "&client_secret=" . $this->settings["fbappsecret"] . "&fb_exchange_token=aaa" . $channel->getToken();
            if($this->get_http_response_code($url) == 200) {
                $token = file_get_contents($url);
                $infos = explode("&", $token);
                $tk = explode("=", $infos[0])[1];
                $exp = time() + explode("=", $infos[1])[1];
                $channel->setToken($tk);
                $channel->setExpires($exp);
            }else{
                if($this->settings["sysmail"]) {
                    $this->sendTokenInfoMail($channel,$this->settings["sysmail"]);
                }
            }
        }*/
        return $channel;
    }

    public function getFeed(\Socialstream\SocialStream\Domain\Model\Channel $channel,$limit=100){
        $this->persistenceManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
        $this->newsRepository = GeneralUtility::makeInstance('Socialstream\\SocialStream\\Domain\\Repository\\NewsRepository');
        $this->categoryRepository = GeneralUtility::makeInstance('GeorgRinger\\News\\Domain\\Repository\\CategoryRepository');

        $url = "https://api.instagram.com/v1/users/".$channel->getObjectId()."/media/recent/?access_token=".$channel->getToken()."&count=".$limit;
        $elem = $this->getElems($url);

        foreach ($elem->data as $entry) {

                $new = 0;

                $id = explode("_",$entry->id);
                if($id[0]){
                    $newsId = $id[0];
                }else{
                    $newsId = $entry->id;
                }

                $news = $this->newsRepository->findHiddenById($newsId, $channel->getUid());

                if (!$news) {
                    $news = new \Socialstream\SocialStream\Domain\Model\News();
                    $new = 1;
                }

                $createTime = new \DateTime();
                $createTime->setTimestamp($entry->created_time);
                $news->setDatetime($createTime);

                $news->setTitle($entry->user->username);

                $news->setType(0);
                $news->setChannel($channel);

                $cat = $this->getCategory($channel->getType());

                $news->addCategory($cat);

                $subcat = $this->getCategory($channel->getTitle() . "@Instagram", $cat);
                $news->addCategory($subcat);

                $news->setObjectId($newsId);

                if ($entry->link) $news->setLink($entry->link);
                $news->setAuthor($entry->user->full_name);


                if ($entry->location) {
                    $news->setPlaceName($entry->location->name);
                    $news->setPlaceLat($entry->location->latitude);
                    $news->setPlaceLng($entry->location->longitude);
                }

                if ($entry->caption->text) {
                    $news->setBodytext($entry->caption->text);
                    $news->setDescription($entry->caption->text);
                }

                if ($new) {
                    $this->newsRepository->add($news);
                } else {
                    $this->newsRepository->update($news);
                }
                $this->persistenceManager->persistAll();

                if($entry->type == "image"){
                    $imageUrl = $entry->images->standard_resolution->url;
                    $news->setMediaUrl($imageUrl);
                    $this->processNewsMedia($news, $imageUrl);
                }else if($entry->type == "video"){
                    $videoUrl = $entry->videos->standard_resolution->url;
                    $news->setMediaUrl($videoUrl);
                    $this->processNewsMedia($news, $videoUrl);
                }else if($entry->type == "carousel"){
                    foreach($entry->carousel_media as $carouselMedia){
                        if($carouselMedia->type == "image"){
                            $imageUrl = $carouselMedia->images->standard_resolution->url;
                            $news->setMediaUrl($imageUrl);
                            $this->processNewsMedia($news, $imageUrl);
                        }else if($carouselMedia->type == "video"){
                            $videoUrl = $carouselMedia->videos->standard_resolution->url;
                            $news->setMediaUrl($videoUrl);
                            $this->processNewsMedia($news, $videoUrl);
                        }
                    }
                }

                $this->newsRepository->update($news);
                $this->persistenceManager->persistAll();
        }
    }
    protected function getCategory($type,\GeorgRinger\News\Domain\Model\Category $parent = NULL){
        $title = $this->getType($type);

        $cat = $this->categoryRepository->findOneByTitle($title);

        if(!$cat){
            $cat = new \GeorgRinger\News\Domain\Model\Category();
            $cat->setTitle($title);
            if($parent)$cat->setParentcategory($parent);
            $this->categoryRepository->add($cat);
            $this->persistenceManager->persistAll();
        }
        return $cat;
    }
}