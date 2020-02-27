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


/**
 * FacebookeventUtility
 */
class FacebookeventUtility extends \Socialstream\SocialStream\Utility\Feed\FacebookUtility
{

    public function getFeed(\Socialstream\SocialStream\Domain\Model\Channel $channel,$limit=100){
        $url = "https://graph.facebook.com/".$channel->getObjectId()."/events?fields=id,start_time,end_time,name,description,place,cover,owner&access_token=".$channel->getToken()."&limit=".$limit;
        $elem = $this->getElems($url);

        foreach ($elem->data as $entry) {
            $new = 0;
            $news = $this->newsRepository->findHiddenById($entry->id,$channel->getUid());
            if (!$news) {
                $news = new \Socialstream\SocialStream\Domain\Model\News();
                $new = 1;
            }

            $news->setType(0);
            $news->setChannel($channel);
            $cat = $this->getCategory($channel->getType(), null, $channel);
            $news->addCategory($cat);
            $subcat = $this->getCategory($channel->getTitle(),$cat, $channel);
            $news->addCategory($subcat);
            $id = explode("_",$entry->id);
            if($id[1]){
                $news->setObjectId($id[1]);
            }else{
                $news->setObjectId($entry->id);
            }
            $news->setDatetime(new \DateTime($entry->start_time));
            $news->setDatetimeend(new \DateTime($entry->end_time));
            if($entry->link)$news->setLink($entry->link);
            $news->setAuthor($entry->owner->name);
            if($entry->name)$news->setTitle($entry->name);
            if(!$news->getPathSegment()) $news->setPathSegment($this->getSlug($news->getUid(),$news->getTitle(), $channel));
            if($entry->place){
                if($entry->place->name) $news->setPlaceName($entry->place->name);
                if($entry->place->location->city) $news->setPlaceCity($entry->place->location->city);
                if($entry->place->location->country) $news->setPlaceCountry($entry->place->location->country);
                if($entry->place->location->latitude) $news->setPlaceLat($entry->place->location->latitude);
                if($entry->place->location->longitude) $news->setPlaceLng($entry->place->location->longitude);
                if($entry->place->location->street) $news->setPlaceStreet($entry->place->location->street);
                if($entry->place->location->zip) $news->setPlaceZip($entry->place->location->zip);
            }

            if($entry->description) {
                $message = str_replace("\n", "<br/>", $entry->description);
                $news->setBodytext(str_replace("<br/><br/>", "<br/>", $message));
            }

            $news->setPid($channel->getPid());

            if ($new) {
                $this->newsRepository->add($news);
            } else {
                $this->newsRepository->update($news);
            }
            $this->persistenceManager->persistAll();

            $imageUrl = '';

            if($entry->cover){
                $imageUrl = $entry->cover->source;
            }

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