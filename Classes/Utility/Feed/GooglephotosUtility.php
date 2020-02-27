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

use Google\Auth\Credentials\UserRefreshCredentials;
use Google\Photos\Library\V1\PhotosLibraryClient;
use Google\Photos\Types\Album;
use Socialstream\SocialStream\Domain\Model\Channel;


/**
 * YoutubeUtility
 */
class GooglephotosUtility extends \Socialstream\SocialStream\Utility\Feed\FeedUtility
{
    /**
     * @param \Socialstream\SocialStream\Domain\Model\Channel $channel
     * @param int $isProcessing
     * @return bool|\Socialstream\SocialStream\Domain\Model\Channel
     */
    public function getChannel(\Socialstream\SocialStream\Domain\Model\Channel $channel, $isProcessing = 0)
    {
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
     * @param Channel $channel
     * @param int $limit
     * @throws \Exception
     */
    public function getFeed(\Socialstream\SocialStream\Domain\Model\Channel $channel, $limit = 50)
    {
        $credentials = new UserRefreshCredentials(
            ['https://www.googleapis.com/auth/photoslibrary.readonly'],
            [
                'client_id' => $this->settings["googlephotosclientid"],
                'client_secret' => $this->settings["googlephotosclientsecret"],
                'refresh_token' => $channel->getRefreshToken()
            ]
        );

        $photosLibraryClient = new PhotosLibraryClient(['credentials' => $credentials]);

        $response = $photosLibraryClient->listAlbums();

        $index = 0;

        $albums = $response->iterateAllElements();

        if ($albums instanceof \Generator) {
            $albums = iterator_to_array($albums);

            if (count($albums) > 0) {
                $albums = array_reverse($albums);
                /** @var Album $entry */
                foreach ($albums as $entry) {
                    $index++;

                    $new = 0;

                    $newsId = $entry->getId();

                    $news = $this->newsRepository->findHiddenById($newsId, $channel->getUid());

                    if (!$news) {
                        $news = new \Socialstream\SocialStream\Domain\Model\News();
                        $new = 1;
                    }

                    $news->setTitle($entry->getTitle());
                    $news->setPid($channel->getPid());
                    $news->setType(0);
                    $news->setChannel($channel);

                    $createTime = new \DateTime();
                    $createTime->modify("+" . $index . " ms");
                    $news->setDatetime($createTime);

                    $cat = $this->getCategory($channel->getType(), null, $channel);

                    $news->addCategory($cat);

                    $subcat = $this->getCategory($channel->getObjectId() . "@GooglePhotos", $cat, $channel);
                    $news->addCategory($subcat);

                    $news->setObjectId($newsId);

                    if (!$news->getPathSegment()) $news->setPathSegment($this->getSlug($news->getUid(), $news->getTitle(), $channel));

                    $news->setLink($entry->getProductUrl());

                    if ($new) {
                        $this->newsRepository->add($news);
                    } else {
                        $this->newsRepository->update($news);
                    }
                    $this->persistenceManager->persistAll();

                    $imageUrl = $entry->getCoverPhotoBaseUrl();
                    $media = $this->validateMedia($channel, $imageUrl);

                    if (is_array($media)) {
                        if ($media['link']) {
                            $news->setMediaUrl($media['link']);
                        }
                        if ($media['media_url']) {
                            $this->processNewsMedia($news, $media['media_url']);
                        }
                    }

                    $this->newsRepository->update($news);
                    $this->persistenceManager->persistAll();
                }
            }
        }
    }
}