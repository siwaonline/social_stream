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
 * Event
 */
class Event extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * id
     *
     * @var string
     */
    protected $id = '';

    /**
     * startTime
     *
     * @var \DateTime
     */
    protected $startTime = null;

    /**
     * endTime
     *
     * @var \DateTime
     */
    protected $endTime = null;

    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * description
     *
     * @var string
     */
    protected $description = '';

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
     * lat
     *
     * @var float
     */
    protected $lat = 0.0;

    /**
     * lng
     *
     * @var float
     */
    protected $lng = 0.0;

    /**
     * pictureUrl
     *
     * @var string
     */
    protected $pictureUrl = '';

    /**
     * picture
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $picture = null;


    /**
     * page
     *
     * @var \Facebookstream\FacebookStream\Domain\Model\Page
     */
    protected $page = null;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param \DateTime $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getPlaceName()
    {
        return $this->placeName;
    }

    /**
     * @param string $placeName
     */
    public function setPlaceName($placeName)
    {
        $this->placeName = $placeName;
    }

    /**
     * @return string
     */
    public function getPlaceStreet()
    {
        return $this->placeStreet;
    }

    /**
     * @param string $placeStreet
     */
    public function setPlaceStreet($placeStreet)
    {
        $this->placeStreet = $placeStreet;
    }

    /**
     * @return string
     */
    public function getPlaceZip()
    {
        return $this->placeZip;
    }

    /**
     * @param string $placeZip
     */
    public function setPlaceZip($placeZip)
    {
        $this->placeZip = $placeZip;
    }

    /**
     * @return string
     */
    public function getPlaceCity()
    {
        return $this->placeCity;
    }

    /**
     * @param string $placeCity
     */
    public function setPlaceCity($placeCity)
    {
        $this->placeCity = $placeCity;
    }

    /**
     * @return string
     */
    public function getPlaceCountry()
    {
        return $this->placeCountry;
    }

    /**
     * @param string $placeCountry
     */
    public function setPlaceCountry($placeCountry)
    {
        $this->placeCountry = $placeCountry;
    }

    /**
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param float $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param float $lng
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    }

    /**
     * @return string
     */
    public function getPictureUrl()
    {
        return $this->pictureUrl;
    }

    /**
     * @param string $pictureUrl
     */
    public function setPictureUrl($pictureUrl)
    {
        $this->pictureUrl = $pictureUrl;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param Page $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }
}