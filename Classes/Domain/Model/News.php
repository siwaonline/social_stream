<?php

namespace Socialstream\SocialStream\Domain\Model;


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
 * News
 */
class News extends \GeorgRinger\News\Domain\Model\News
{

    /**
     * objectId
     *
     * @var string
     */
    protected $objectId = '';

    /**
     * link
     *
     * @var string
     */
    protected $link = '';

    /**
     * mediaUrl
     *
     * @var string
     */
    protected $mediaUrl = '';

    /**
     * placeName
     *
     * @var string
     */
    protected $placeName = '';

    /**
     * placeStreet
     *
     * @var string
     */
    protected $placeStreet = '';

    /**
     * placeZip
     *
     * @var string
     */
    protected $placeZip = '';

    /**
     * placeCity
     *
     * @var string
     */
    protected $placeCity = '';

    /**
     * placeCountry
     *
     * @var string
     */
    protected $placeCountry = '';

    /**
     * placeLat
     *
     * @var string
     */
    protected $placeLat = '';

    /**
     * placeLng
     *
     * @var string
     */
    protected $placeLng = '';

    /**
     * @var \DateTime
     */
    protected $datetimeend;

    /**
     * channel
     *
     * @var \Socialstream\SocialStream\Domain\Model\Channel
     */
    protected $channel = null;

    /**
     * Returns the objectId
     *
     * @return string $objectId
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Sets the objectId
     *
     * @param string $objectId
     * @return void
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;
    }

    /**
     * Returns the link
     *
     * @return string $link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Sets the link
     *
     * @param string $link
     * @return void
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->mediaUrl;
    }

    /**
     * @param string $mediaUrl
     */
    public function setMediaUrl($mediaUrl)
    {
        $this->mediaUrl = $mediaUrl;
    }

    /**
     * Returns the placeName
     *
     * @return string $placeName
     */
    public function getPlaceName()
    {
        return $this->placeName;
    }

    /**
     * Sets the placeName
     *
     * @param string $placeName
     * @return void
     */
    public function setPlaceName($placeName)
    {
        $this->placeName = $placeName;
    }

    /**
     * Returns the placeStreet
     *
     * @return string $placeStreet
     */
    public function getPlaceStreet()
    {
        return $this->placeStreet;
    }

    /**
     * Sets the placeStreet
     *
     * @param string $placeStreet
     * @return void
     */
    public function setPlaceStreet($placeStreet)
    {
        $this->placeStreet = $placeStreet;
    }

    /**
     * Returns the placeZip
     *
     * @return string $placeZip
     */
    public function getPlaceZip()
    {
        return $this->placeZip;
    }

    /**
     * Sets the placeZip
     *
     * @param string $placeZip
     * @return void
     */
    public function setPlaceZip($placeZip)
    {
        $this->placeZip = $placeZip;
    }

    /**
     * Returns the placeCity
     *
     * @return string $placeCity
     */
    public function getPlaceCity()
    {
        return $this->placeCity;
    }

    /**
     * Sets the placeCity
     *
     * @param string $placeCity
     * @return void
     */
    public function setPlaceCity($placeCity)
    {
        $this->placeCity = $placeCity;
    }

    /**
     * Returns the placeCountry
     *
     * @return string $placeCountry
     */
    public function getPlaceCountry()
    {
        return $this->placeCountry;
    }

    /**
     * Sets the placeCountry
     *
     * @param string $placeCountry
     * @return void
     */
    public function setPlaceCountry($placeCountry)
    {
        $this->placeCountry = $placeCountry;
    }

    /**
     * Returns the placeLat
     *
     * @return string $placeLat
     */
    public function getPlaceLat()
    {
        return $this->placeLat;
    }

    /**
     * Sets the placeLat
     *
     * @param string $placeLat
     * @return void
     */
    public function setPlaceLat($placeLat)
    {
        $this->placeLat = $placeLat;
    }

    /**
     * Returns the placeLng
     *
     * @return string $placeLng
     */
    public function getPlaceLng()
    {
        return $this->placeLng;
    }

    /**
     * Sets the placeLng
     *
     * @param string $placeLng
     * @return void
     */
    public function setPlaceLng($placeLng)
    {
        $this->placeLng = $placeLng;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimeend()
    {
        return $this->datetimeend;
    }

    /**
     * @param \DateTime $datetimeend
     */
    public function setDatetimeend($datetimeend)
    {
        $this->datetimeend = $datetimeend;
    }

    /**
     * Returns the channel
     *
     * @return \Socialstream\SocialStream\Domain\Model\Channel $channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Sets the channel
     *
     * @param \Socialstream\SocialStream\Domain\Model\Channel $channel
     * @return void
     */
    public function setChannel(\Socialstream\SocialStream\Domain\Model\Channel $channel)
    {
        $this->channel = $channel;
    }

}