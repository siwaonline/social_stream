<?php

namespace Socialstream\SocialStream\Utility\Feed;

use InvalidArgumentException;
use LogicException;
use Exception as GlobalException;
use RuntimeException;
use Socialstream\SocialStream\Domain\Model\Channel;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Core\Resource\Exception\ExistingTargetFolderException;
use TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException;
use TYPO3\CMS\Core\Resource\Exception\InsufficientFolderWritePermissionsException;
use UnexpectedValueException;

/**
 * Connects with a given Token and given URL to the Involve Facebook API and stores retrieved Posts as News
 * FacebookinvolveUtility
 */
class FacebookinvolveUtility extends \Socialstream\SocialStream\Utility\Feed\FeedUtility
{
    /**
     * Returns the given channel - no need to do something extra
     * @param Channel $channel 
     * @param int $isProcessing 
     * @return Channel 
     */
    public function getChannel(\Socialstream\SocialStream\Domain\Model\Channel $channel, $isProcessing = 0)
    {
        // static
        return $channel;
    }

    /**
     * Returns the given channel - no need to do something extra
     * @param Channel $channel 
     * @return Channel 
     */
    public function renewToken(\Socialstream\SocialStream\Domain\Model\Channel $channel)
    {
        // Token is static
        return $channel;
    }

    /**
     * 
     * @param Channel $channel 
     * @param int $limit 
     * @return void 
     * @throws IllegalObjectTypeException 
     * @throws UnknownObjectException 
     * @throws InvalidArgumentException 
     * @throws LogicException 
     * @throws Exception 
     * @throws ExistingTargetFolderException 
     * @throws InsufficientFolderAccessPermissionsException 
     * @throws InsufficientFolderWritePermissionsException 
     * @throws GlobalException 
     * @throws UnexpectedValueException 
     * @throws RuntimeException 
     */
    public function getFeed(\Socialstream\SocialStream\Domain\Model\Channel $channel, $limit = 100)
    {
        $url = $channel->getObjectId() + (str_contains($channel->getObjectId(), '?') ? '&' : '?') + 'token=' + $channel->getToken();
        $elem = $this->getElems($url);

        foreach ($elem->data as $entry) {
            if ($entry->title || $entry->text) {
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

                if ($entry->permalink){
                    $news->setLink($entry->permalink);
                }
                if ($entry->title) {
                    $news->setTitle($entry->title);
                }

                if(!$news->getPathSegment()) {
                    $news->setPathSegment($this->getSlug($news->getUid(),$news->getTitle(), $channel));
                }

                if ($entry->message) {
                    $news->setBodytext($entry->message);
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
                debug($news);
                exit;


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
                debug($news);
                exit;

                $this->newsRepository->update($news);
                $this->persistenceManager->persistAll();
            }
        }
    }
}