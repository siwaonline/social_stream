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
 * Post
 */
class Post extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * id
     *
     * @var string
     */
    protected $id = '';
    
    /**
     * objectId
     *
     * @var string
     */
    protected $objectId = '';
    
    /**
     * createdTime
     *
     * @var \DateTime
     */
    protected $createdTime = null;
    
    /**
     * link
     *
     * @var string
     */
    protected $link = '';
    
    /**
     * type
     *
     * @var int
     */
    protected $type = 0;
    
    /**
     * name
     *
     * @var string
     */
    protected $name = '';
    
    /**
     * caption
     *
     * @var string
     */
    protected $caption = '';
    
    /**
     * description
     *
     * @var string
     */
    protected $description = '';

    /**
     * message
     *
     * @var string
     */
    protected $message = '';

    /**
     * story
     *
     * @var string
     */
    protected $story = '';

    /**
     * metatitle
     *
     * @var string
     */
    protected $metatitle = '';

    /**
     * metadesc
     *
     * @var string
     */
    protected $metadesc = '';

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
     * videoUrl
     *
     * @var string
     */
    protected $videoUrl = '';

    /**
     * video
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $video = null;
    
    /**
     * page
     *
     * @var \Socialstream\SocialStream\Domain\Model\Page
     */
    protected $page = null;
    
    /**
     * Returns the id
     *
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Returns the id
     *
     * @return string $id
     */
    public function getPostId()
    {
        $id = explode("_",$this->id);
        return $id[1];
    }
    
    /**
     * Sets the id
     *
     * @param string $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    
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
     * Returns the createdTime
     *
     * @return \DateTime $createdTime
     */
    public function getCreatedTime()
    {
        return $this->createdTime;
    }
    
    /**
     * Sets the createdTime
     *
     * @param \DateTime $createdTime
     * @return void
     */
    public function setCreatedTime(\DateTime $createdTime)
    {
        $this->createdTime = $createdTime;
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
     * Returns the type
     *
     * @return int $type
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Sets the type
     *
     * @param int $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    
    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Returns the caption
     *
     * @return string $caption
     */
    public function getCaption()
    {
        return $this->caption;
    }
    
    /**
     * Sets the caption
     *
     * @param string $caption
     * @return void
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }
    
    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    /**
     * Returns the message
     *
     * @return string $message
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * Sets the message
     *
     * @param string $message
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getStory()
    {
        return $this->story;
    }

    /**
     * @param string $story
     */
    public function setStory($story)
    {
        $this->story = $story;
    }

    /**
     * @return string
     */
    public function getMetatitle()
    {
        return $this->metatitle;
    }

    /**
     * @param string $metatitle
     */
    public function setMetatitle($metatitle)
    {
        $this->metatitle = $metatitle;
    }

    /**
     * @return string
     */
    public function getMetadesc()
    {
        return $this->metadesc;
    }

    /**
     * @param string $metadesc
     */
    public function setMetadesc($metadesc)
    {
        $this->metadesc = $metadesc;
    }
    
    /**
     * Returns the pictureUrl
     *
     * @return string $pictureUrl
     */
    public function getPictureUrl()
    {
        return $this->pictureUrl;
    }
    
    /**
     * Sets the pictureUrl
     *
     * @param string $pictureUrl
     * @return void
     */
    public function setPictureUrl($pictureUrl)
    {
        $this->pictureUrl = $pictureUrl;
    }
    
    /**
     * Returns the picture
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $picture
     */
    public function getPicture()
    {
        return $this->picture;
    }
    
    /**
     * Sets the picture
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $picture
     * @return void
     */
    public function setPicture(\TYPO3\CMS\Extbase\Domain\Model\FileReference $picture)
    {
        $this->picture = $picture;
    }

    /**
     * @return string
     */
    public function getVideoUrl()
    {
        return $this->videoUrl;
    }

    /**
     * @param string $videoUrl
     */
    public function setVideoUrl($videoUrl)
    {
        $this->videoUrl = $videoUrl;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $video
     */
    public function setVideo($video)
    {
        $this->video = $video;
    }
    
    /**
     * Returns the page
     *
     * @return \Socialstream\SocialStream\Domain\Model\Page $page
     */
    public function getPage()
    {
        return $this->page;
    }
    
    /**
     * Sets the page
     *
     * @param \Socialstream\SocialStream\Domain\Model\Page $page
     * @return void
     */
    public function setPage(\Socialstream\SocialStream\Domain\Model\Page $page)
    {
        $this->page = $page;
    }

}