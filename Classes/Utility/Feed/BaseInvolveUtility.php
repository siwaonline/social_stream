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

use \TYPO3\CMS\Core\Messaging\FlashMessageService;
use \TYPO3\CMS\Core\Messaging\FlashMessage;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * InstagramUtility
 */
class BaseInvolveUtility extends \Socialstream\SocialStream\Utility\Feed\FeedUtility
{
    protected $apiUrl = '';

    /**
     * @param \Socialstream\SocialStream\Domain\Model\Channel $channel
     * @param int $isProcessing
     * @return bool|\Socialstream\SocialStream\Domain\Model\Channel
     */
    public function getChannel(\Socialstream\SocialStream\Domain\Model\Channel $channel, $isProcessing = 0)
    {
        $url = $this->settings['involveAPIUrl'] . $this->apiUrl . $channel->getObjectId() . (strpos($channel->getObjectId(), '?') !== false ? '&' : '?') . 'token=' . $channel->getToken();
        $elem = $this->getElems($url, true);
        if ($elem && $elem[0]) {
            $entry = $elem[0];
            if ($entry->title && $channel->getTitle() !== $entry->title) {
                $channel->setTitle($entry->title);
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
        // Token is static
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
        $url = $this->settings['involveAPIUrl'] . $this->apiUrl .  $channel->getObjectId() . (strpos($channel->getObjectId(), '?') !== false ? '&' : '?') . 'token=' . $channel->getToken();

        $requestFactory = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Http\RequestFactory::class);
        $response = $requestFactory->request($url, 'GET');
        if ($response->getStatusCode() === 200) {
            if (strpos($response->getHeaderLine('Content-Type'), 'application/json') === 0) {
                if ($response instanceof \GuzzleHttp\Psr7\Response) {
                    $elem = json_decode($response->getBody()->getContents());
                }
            }
        }


        foreach ($elem as $entry) {
            if ($entry->title || $entry->text) {
                $this->persistNewsFromEntry($channel, $entry);
            }
        }
    }

    function persistNewsFromEntry($channel, $entry){
        $hash = md5($entry->createdAt);


        $new = 0;
        $news = $this->newsRepository->findHiddenById($hash, $channel->getUid(), 0);
        if (!$news) {
            $news = new \Socialstream\SocialStream\Domain\Model\News();
            $new = 1;
        }
        $news->setObjectId($hash);
        $news->setType(0);
        $news->setChannel($channel);

        $cat = $this->getCategory($channel->getType(), null, $channel);
        $news->addCategory($cat);

        $subcat = $this->getCategory($channel->getTitle(), $cat, $channel);
        $news->addCategory($subcat);

        $news->setDatetime(new \DateTime($entry->createdAt));
        $news->setAuthor($entry->title);

        if(!$news->getPathSegment()) $news->setPathSegment($this->getSlug($news->getUid(),$news->getTitle(), $channel));

        if ($entry->permalink) {
            $news->setLink($entry->permalink);
        }
        if ($entry->title) {
            $news->setTitle($entry->title);
        }

        if ($entry->text) {
            $news->setBodytext($entry->text);
        } else {
            if ($entry->title) {
                $news->setBodytext($entry->title);
            }
        }


        $news->setPid($channel->getPid());

        if ($new) {
            $this->newsRepository->add($news);
        } else {
            $this->newsRepository->update($news);
        }
        $this->persistenceManager->persistAll();

        $imageUrl = '';
        $videoUrl = '';

        if ($entry->attachedVideoUrl) {
            $videoUrl = $entry->attachedVideoUrl;
        }

        if ($entry->attachedImageUrl) {
            $imageUrl = $entry->attachedImageUrl;
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

        $this->newsRepository->update($news);
        $this->persistenceManager->persistAll();
    }
}
