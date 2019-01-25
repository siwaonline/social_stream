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

use Socialstream\SocialStream\Domain\Model\Channel;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\YouTubeHelper;
use \TYPO3\CMS\Core\Messaging\FlashMessageService;
use \TYPO3\CMS\Core\Messaging\FlashMessage;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * YoutubeUtility
 */
class YoutubeUtility extends \Socialstream\SocialStream\Utility\Feed\FeedUtility
{
    /**
     * @param \Socialstream\SocialStream\Domain\Model\Channel $channel
     * @param int $isProcessing
     * @return bool|\Socialstream\SocialStream\Domain\Model\Channel
     */
    public function getChannel(\Socialstream\SocialStream\Domain\Model\Channel $channel, $isProcessing = 0)
    {
        $response = $this->loadChannel($channel, $isProcessing);
        if ($response->items && is_array($response->items)) {
            $youtubeChannel = $response->items[0];
            if ($youtubeChannel->kind === "youtube#channel") {
                if ($youtubeChannel->snippet) {
                    $channel->setTitle($youtubeChannel->snippet->title);
                    $channel->setDescription($youtubeChannel->snippet->description);
                    $channel->setLink("https://www.youtube.com/" . $youtubeChannel->snippet->customUrl);
                    if ($youtubeChannel->snippet->thumbnails) {
                        if ($isProcessing == 0) {
                            $imageUrl = $youtubeChannel->snippet->thumbnails->high->url . ".jpg";
                            if ($this->exists($imageUrl)) {
                                $this->processChannelMedia($channel, $imageUrl);
                            }
                        }
                    }
                }
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
        if (!empty($channel->getToken()) && !empty($channel->getRefreshToken())) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.googleapis.com/oauth2/v4/token",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "grant_type=refresh_token&refresh_token=" . urlencode($channel->getRefreshToken()) . "&client_id=" . $this->settings["youtubeclientid"] . "&client_secret=" . $this->settings["youtubeclientsecret"],
                CURLOPT_HTTPHEADER => array(
                    "Cache-Control: no-cache",
                    "Content-Type: application/x-www-form-urlencoded"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                if ($this->settings["sysmail"]) {
                    $this->sendTokenInfoMail($channel, $this->settings["sysmail"], $this->settings["sendermail"]);
                }
            } else {
                $jsonResponse = json_decode($response);
                $channel->setToken($jsonResponse->access_token);
                $channel->setExpires($jsonResponse->expires);
            }
        }
        return $channel;
    }

    /**
     * @param Channel $channel
     * @param int $limit
     * @throws \Exception
     */
    public function getFeed(\Socialstream\SocialStream\Domain\Model\Channel $channel, $limit = 50)
    {
        $this->persistenceManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
        $this->newsRepository = GeneralUtility::makeInstance('Socialstream\\SocialStream\\Domain\\Repository\\NewsRepository');
        $this->categoryRepository = GeneralUtility::makeInstance('GeorgRinger\\News\\Domain\\Repository\\CategoryRepository');

        $response = $this->loadChannel($channel, 1);

        if ($response->items && is_array($response->items)) {
            $youtubeChannel = $response->items[0];
            if ($youtubeChannel->kind === "youtube#channel") {
                if ($youtubeChannel->contentDetails) {
                    if ($playlist = $youtubeChannel->contentDetails->relatedPlaylists) {
                        $url = "https://www.googleapis.com/youtube/v3/playlistItems/?playlistId=" . $playlist->uploads . "&maxResults=" . $limit . "&part=snippet%2CcontentDetails&key=" . $this->settings["youtubeapikey"];
                        $playlistItemList = $this->getElemsCurl($url);
                        if ($playlistItemList->kind === "youtube#playlistItemListResponse") {
                            foreach ($playlistItemList->items as $playlistItem) {
                                if ($playlistItem->snippet) {
                                    $new = 0;

                                    $news = $this->newsRepository->findHiddenById($playlistItem->snippet->resourceId->videoId, $channel->getUid());

                                    if (!$news) {
                                        $news = new \Socialstream\SocialStream\Domain\Model\News();
                                        $new = 1;
                                    }
                                    $createTime = new \DateTime($playlistItem->snippet->publishedAt);
                                    $news->setDatetime($createTime);

                                    $news->setTitle($playlistItem->snippet->title);

                                    $news->setType(0);
                                    $news->setChannel($channel);

                                    $cat = $this->getCategory($channel->getType());

                                    $news->addCategory($cat);

                                    $subcat = $this->getCategory($channel->getTitle() . "@YouTube", $cat);
                                    $news->addCategory($subcat);

                                    $news->setObjectId($playlistItem->snippet->resourceId->videoId);

                                    $news->setLink("https://www.youtube.com/watch?v=" . $playlistItem->snippet->resourceId->videoId);
                                    $news->setAuthor($playlistItem->snippet->channelTitle);

                                    $news->setBodytext($playlistItem->snippet->description);
                                    $news->setDescription($playlistItem->snippet->description);

                                    if ($new) {
                                        $this->newsRepository->add($news);
                                    } else {
                                        $this->newsRepository->update($news);
                                    }
                                    $this->persistenceManager->persistAll();

                                    $youTubeHelper = new YouTubeHelper('youtube');

                                    $folder = $this->getSubFolder($this->getSubFolder($this->getSubFolder($this->getMainFolder(), $news->getChannel()->getType()), $news->getChannel()->getObjectId()), "news");

                                    if ($youTubeFile = $youTubeHelper->transformUrlToFile("https://www.youtube.com/watch?v=" . $playlistItem->snippet->resourceId->videoId, $folder)) {
                                        if (count($news->getFalMedia()) > 0) {
                                            /*$media = $news->getFalMedia()->current();
                                            if ($media) {
                                                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file_reference');
                                                $queryBuilder
                                                    ->update('sys_file_reference')
                                                    ->where(
                                                        $queryBuilder->expr()->eq('uid', $media->getUid())
                                                    )
                                                    ->set('deleted', 1)
                                                    ->execute();
                                            }*/
                                        }else {
                                            $data = array();
                                            $data['sys_file_reference']['NEW12345'] = array(
                                                'uid_local' => $youTubeFile->getUid(),
                                                'uid_foreign' => $news->getUid(),
                                                'tablenames' => 'tx_news_domain_model_news',
                                                'fieldname' => 'fal_media',
                                                'pid' => $this->settings["storagePid"],
                                                'table_local' => 'sys_file',
                                                'showinpreview' => 1,
                                            );
                                            $data['tx_news_domain_model_news'][$news->getUid()] = array('fal_media' => 'NEW12345');

                                            /** @var \TYPO3\CMS\Core\DataHandling\DataHandler $tce */
                                            $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler');
                                            $tce->bypassAccessCheckForRecords = TRUE;
                                            $tce->start($data, array());
                                            $tce->admin = TRUE;
                                            $tce->process_datamap();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param Channel $channel
     * @param int $isProcessing
     * @return bool|mixed
     */
    protected function loadChannel(Channel $channel, $isProcessing = 0)
    {
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

        if ($channel->getObjectId() && $this->settings["youtubeapikey"]) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.googleapis.com/youtube/v3/channels/?id=" . $channel->getObjectId() . "&part=snippet%2CcontentDetails%2Cstatistics&key=" . $this->settings["youtubeapikey"],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Cache-Control: no-cache"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                if ($isProcessing == 0) {
                    if ($this->settings["sysmail"]) {
                        $this->sendTokenInfoMail($channel, $this->settings["sysmail"], $this->settings["sendermail"]);
                    }
                } else {
                    $this->addFlashMessage($err, '', FlashMessage::ERROR, $objectManager->get(FlashMessageService::class));
                }
            } else {
                return json_decode($response);
            }
        } else {
            if ($isProcessing == 0) {
                if ($this->settings["sysmail"]) {
                    $this->sendTokenInfoMail($channel, $this->settings["sysmail"], $this->settings["sendermail"]);
                }
            } else {
                $this->addFlashMessage("No Object ID or Google API Key - Type: " . $channel->getType(), '', FlashMessage::ERROR, $objectManager->get(FlashMessageService::class));
            }
        }
        return false;
    }
}