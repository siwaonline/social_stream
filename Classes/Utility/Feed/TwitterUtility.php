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

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;

/**
 * TwitterUtility
 */
class TwitterUtility extends \Socialstream\SocialStream\Utility\Feed\FeedUtility
{

    public function getChannel(\Socialstream\SocialStream\Domain\Model\Channel $channel,$isProcessing=0)
    {
        $ch = curl_init("https://api.twitter.com/1.1/users/show.json?user_id=" . $channel->getObjectId());
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer '. $channel->getToken()
        );

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30
        );

        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);

        $requestInfo = curl_getinfo($ch);

        if($requestInfo["http_code"] == 200) {

            $elem = json_decode($result,true);

            $channel->setObjectId($elem["id_str"]);
            $channel->setTitle($elem["screen_name"]);
            if ($elem["description"]) $channel->setAbout($elem["description"]);
            $channel->setLink("https://www.twitter.com/" . $elem["screen_name"] . "/");

            if ($isProcessing == 0) {
                $imageUrl = $elem["profile_image_url"];
                $imageUrl = str_replace('_normal', '', $imageUrl);
                if ($this->exists($imageUrl)) {
                    $this->processChannelMedia($channel, $imageUrl);
                }
            }
        }else{
            if ($isProcessing == 0) {
                if($this->settings["sysmail"]) {
                    $this->sendTokenInfoMail($channel,$this->settings["sysmail"],$this->settings["sendermail"]);
                }
            }else{
                $msg = "Fehler: Channel konnte nicht gecrawlt werden. Object Id oder Token falsch.";
                $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
                $this->addFlashMessage($msg, '', FlashMessage::ERROR,$this->objectManager->get(FlashMessageService::class));
                return false;
            }
        }

        return $channel;
    }

    public function renewToken(\Socialstream\SocialStream\Domain\Model\Channel $channel){
        return $channel;
    }

    public function getFeed(\Socialstream\SocialStream\Domain\Model\Channel $channel,$limit=100){
        if($channel->getPosttype() == "1"){
            $url = "https://api.twitter.com/1.1/statuses/user_timeline.json?user_id=" . $channel->getObjectId() . "&count=" . $limit . "&exclude_replies=true";
        }else{
            $url = "https://api.twitter.com/1.1/statuses/user_timeline.json?user_id=" . $channel->getObjectId() . "&count=" . $limit;
        }

        $ch = curl_init($url);

        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer '. $channel->getToken()
        );

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30
        );

        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);

        $requestInfo = curl_getinfo($ch);

        if($requestInfo["http_code"] == 200) {
            $elem = json_decode($result,true);

            foreach ($elem as $entry) {

                $new = 0;

                $id = explode("_",$entry["id_str"]);
                if($id[0]){
                    $newsId = $id[0];
                }else{
                    $newsId = $entry["id_str"];
                }

                $news = $this->newsRepository->findHiddenById($newsId, $channel->getUid());

                if (!$news) {
                    $news = new \Socialstream\SocialStream\Domain\Model\News();
                    $new = 1;
                }

                $createTime = new \DateTime($entry["created_at"]);
                $news->setDatetime($createTime);

                $news->setTitle($entry["user"]["screen_name"]);

                $news->setType(0);
                $news->setChannel($channel);

                $cat = $this->getCategory($channel->getType());

                $news->addCategory($cat);

                $subcat = $this->getCategory($channel->getTitle() . "@Twitter", $cat);
                $news->addCategory($subcat);

                $news->setObjectId($newsId);
                if(!$news->getPathSegment()) $news->setPathSegment($this->getSlug($news->getUid(),$news->getTitle()));

                $news->setLink("https://www.twitter.com/" . $entry["user"]["screen_name"] . "/status/" . $entry["id_str"]);
                $news->setAuthor($entry["user"]["name"]);


                if ($entry["place"]) {
                    $news->setPlaceName($entry["place"]["full_name"]);
                }

                if ($entry["text"]) {
                    $news->setBodytext($entry["text"]);
                    $news->setDescription($entry["text"]);
                }

                $news->setPid($this->getStoragePid());

                if ($new) {
                    $this->newsRepository->add($news);
                } else {
                    $this->newsRepository->update($news);
                }
                $this->persistenceManager->persistAll();

                if($entry["entities"]["media"]){
                    $mediaUrlSet = false;
                    foreach($entry["entities"]["media"] as $media){
                        $imageUrl = '';
                        if($media["type"] == "photo"){
                            $imageUrl = $media["media_url"];
                        }
                        $media = $this->validateMedia($channel, $imageUrl);

                        if(is_array($media)){
                            if($media['link']){
                                if(!$mediaUrlSet){
                                    $news->setMediaUrl($media['link']);
                                    $mediaUrlSet = true;
                                }

                            }
                            if($media['media_url']){
                                $this->processNewsMedia($news, $media['media_url']);
                            }
                        }
                    }
                }

                $this->newsRepository->update($news);
                $this->persistenceManager->persistAll();
            }

        }
    }
}