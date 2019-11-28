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
 * InstagramUtility
 */
class InstagramUtility extends \Socialstream\SocialStream\Utility\Feed\FeedUtility
{

    /**
     * @param \Socialstream\SocialStream\Domain\Model\Channel $channel
     * @param int $isProcessing
     * @return bool|\Socialstream\SocialStream\Domain\Model\Channel
     */
    public function getChannel(\Socialstream\SocialStream\Domain\Model\Channel $channel, $isProcessing = 0)
    {
        $url = "https://api.instagram.com/v1/users/" . $channel->getObjectId() . "/?access_token=" . $channel->getToken();
        if ($this->get_http_response_code($url) == 200) {
            $elem = $this->getElems($url);


            $channel->setObjectId($elem->data->id);
            $channel->setTitle($elem->data->username);
            if ($elem->data->bio) $channel->setAbout($elem->data->bio);
            $channel->setLink("https://www.instagram.com/" . $elem->data->username . "/");

            if ($isProcessing == 0) {
                $imageUrl = $elem->data->profile_picture;
                if ($this->exists($imageUrl)) {
                    $this->processChannelMedia($channel, $imageUrl);
                }
            }
        } else {
            if ($isProcessing !== 0) {
                $msg = "Fehler: Channel konnte nicht gecrawlt werden. Object Id oder Token falsch.";
                $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
                $this->addFlashMessage($msg, '', FlashMessage::ERROR, $this->objectManager->get(FlashMessageService::class));
                return false;
            }
        }

        return $channel;
    }

    /**
     * @param \Socialstream\SocialStream\Domain\Model\Channel $channel
     * @return \Socialstream\SocialStream\Domain\Model\Channel
     */
    public function renewToken(\Socialstream\SocialStream\Domain\Model\Channel $channel)
    {
        return $channel;
    }

    /**
     * @param \Socialstream\SocialStream\Domain\Model\Channel $channel
     * @param int $limit
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function getFeed(\Socialstream\SocialStream\Domain\Model\Channel $channel, $limit = 100)
    {
        $url = "https://api.instagram.com/v1/users/" . $channel->getObjectId() . "/media/recent/?access_token=" . $channel->getToken() . "&count=" . $limit;
        $elem = $this->getElems($url);

        foreach ($elem->data as $entry) {

            $new = 0;

            $id = explode("_", $entry->id);
            if ($id[0]) {
                $newsId = $id[0];
            } else {
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
            if(!$news->getPathSegment()) $news->setPathSegment($this->getSlug($news->getUid(),$news->getTitle()));

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

            $news->setPid($this->getStoragePid());

            if ($new) {
                $this->newsRepository->add($news);
            } else {
                $this->newsRepository->update($news);
            }
            $this->persistenceManager->persistAll();

            $imageUrl = $entry->images->standard_resolution->url;
            $videoUrl = '';

            if ($entry->type == "video") {
                $videoUrl = $entry->videos->standard_resolution->url;
            }

            $media = $this->validateMedia($channel, $imageUrl, $videoUrl);

            if (is_array($media)) {
                if ($media['link']) {
                    $news->setMediaUrl($media['link']);
                }
                if ($media['media_url']) {
                    $this->processNewsMedia($news, $media['media_url']);
                }
            }

            //TODO: processNewsMedia always uses the first image so a carousel needs a new logic
//            if ($entry->type == "carousel") {
//                $mediaUrlSet = false;
//                foreach ($entry->carousel_media as $carouselMedia) {
//                    $imageUrl = '';
//                    $videoUrl = '';
//
//                    if ($carouselMedia->type == "image") {
//                        $imageUrl = $carouselMedia->images->standard_resolution->url;
//                    } else if ($carouselMedia->type == "video") {
//                        $imageUrl = $entry->images->standard_resolution->url;
//                        $videoUrl = $carouselMedia->videos->standard_resolution->url;
//                    }
//
//                    $media = $this->validateMedia($channel, $imageUrl, $videoUrl);
//
//                    if (is_array($media)) {
//                        if ($media['link']) {
//                            if (!$mediaUrlSet) {
//                                $news->setMediaUrl($media['link']);
//                                $mediaUrlSet = true;
//                            }
//                        }
//                        if ($media['media_url']) {
//                            $this->processNewsMedia($news, $media['media_url']);
//                        }
//                    }
//                }
//            }

            $this->newsRepository->update($news);
            $this->persistenceManager->persistAll();
        }
    }
}