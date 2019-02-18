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
 * FlickrUtility
 */
class FlickrUtility extends \Socialstream\SocialStream\Utility\Feed\FeedUtility
{

    public function getChannel(\Socialstream\SocialStream\Domain\Model\Channel $channel,$isProcessing=0)
    {

        $url = "https://api.flickr.com/services/rest/?method=flickr.people.getInfo&api_key=" . $channel->getToken() . "&user_id=" . $channel->getObjectId() . "&format=json&nojsoncallback=1";

        if($this->get_http_response_code($url) == 200) {
            $elem = $this->getElems($url);

            $channel->setTitle($elem->person->realname->_content);
            if ($elem->about) $channel->setAbout($elem->person->description->_content);
            $channel->setLink($elem->person->profileurl->_content);

            if ($isProcessing == 0) {
                $imageUrl = "http://farm" . $elem->person->iconfarm . ".staticflickr.com/" . $elem->person->iconserver . "/buddyicons/" . $elem->person->id . ".jpg";
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
                //$this->addFlashMessage($msg, '', AbstractMessage::ERROR);
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
        $url = "https://api.flickr.com/services/rest/?method=flickr.photosets.getList&api_key=" . $channel->getToken() . "&user_id=" . $channel->getObjectId() . "&format=json&nojsoncallback=1";


        $elem = $this->getElems($url);

        foreach ($elem->photosets->photoset as $entry) {

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
            $createTime->setTimestamp($entry->date_create);
            $news->setDatetime($createTime);

            $news->setTitle($entry->title->_content);

            $news->setType(0);
            $news->setChannel($channel);

            $cat = $this->getCategory($channel->getType());

            $news->addCategory($cat);

            $subcat = $this->getCategory($channel->getTitle() . "@Flickr", $cat);
            $news->addCategory($subcat);

            $news->setObjectId($newsId);
            if(!$news->getPathSegment()) $news->setPathSegment($this->getSlug($news->getUid(),$news->getTitle()));

            $news->setLink("https://www.flickr.com/photos/vpnoeat/albums/" . $entry->id);

            if ($entry->description) {
                $news->setBodytext($entry->description->_content);
                $news->setDescription($entry->description->_content);
            }

            $news->setPid($this->getStoragePid());

            if ($new) {
                $this->newsRepository->add($news);
            } else {
                $this->newsRepository->update($news);
            }
            $this->persistenceManager->persistAll();

            $imageUrl = '';
            if($entry->farm && $entry->server && $entry->primary && $entry->secret)
            $imageUrl = "https://farm" . $entry->farm . ".static.flickr.com/" . $entry->server . "/" . $entry->primary . "_" . $entry->secret . "_b.jpg";

            $media = $this->validateMedia($channel, $imageUrl);

            if(is_array($media)){
                if($media['link']){
                    $news->setMediaUrl($media['link']);
                }
                if($media['media_url']){
                    $this->processNewsMedia($news, $media['media_url']);
                }
            }

            $this->newsRepository->update($news);
            $this->persistenceManager->persistAll();
        }
    }
}