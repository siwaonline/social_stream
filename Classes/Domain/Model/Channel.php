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
 * Channel
 */
class Channel extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * objectId
     *
     * @var string
     */
    protected $objectId = '';
    
    /**
     * title
     *
     * @var string
     */
    protected $title = '';
    
    /**
     * about
     *
     * @var string
     */
    protected $about = '';
    
    /**
     * description
     *
     * @var string
     */
    protected $description = '';
    
    /**
     * user
     *
     * @var bool
     */
    protected $user = false;
    
    /**
     * type
     *
     * @var string
     */
    protected $type = '';

    /**
     * posttype
     *
     * @var string
     */
    protected $posttype = '';

    /**
     * videosync
     *
     * @var bool
     */
    protected $videosync = '';
    
    /**
     * link
     *
     * @var string
     */
    protected $link = '';
    
    /**
     * image
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $image = null;
    
    /**
     * token
     *
     * @var string
     */
    protected $token = '';

    /**
     * refreshToken
     *
     * @var string
     */
    protected $refreshToken = '';
    
    /**
     * expires
     *
     * @var int
     */
    protected $expires = 0;
    
    /**
     * news
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Socialstream\SocialStream\Domain\Model\News>
     * @cascade remove
     */
    protected $news = null;
    
    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }
    
    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->news = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
     * Returns the about
     *
     * @return string $about
     */
    public function getAbout()
    {
        return $this->about;
    }
    
    /**
     * Sets the about
     *
     * @param string $about
     * @return void
     */
    public function setAbout($about)
    {
        $this->about = $about;
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
     * Returns the user
     *
     * @return bool $user
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Sets the user
     *
     * @param bool $user
     * @return void
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
    
    /**
     * Returns the boolean state of user
     *
     * @return bool
     */
    public function isUser()
    {
        return $this->user;
    }
    
    /**
     * Returns the type
     *
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Sets the type
     *
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getPosttype()
    {
        return $this->posttype;
    }

    /**
     * @param string $posttype
     */
    public function setPosttype($posttype)
    {
        $this->posttype = $posttype;
    }

    /**
     * @return bool
     */
    public function isVideosync()
    {
        return $this->videosync;
    }

    /**
     * @param bool $videosync
     */
    public function setVideosync($videosync)
    {
        $this->videosync = $videosync;
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
     * Returns the image
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     */
    public function getImage()
    {
        return $this->image;
    }
    
    /**
     * Sets the image
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     * @return void
     */
    public function setImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image)
    {
        $this->image = $image;
    }
    
    /**
     * Returns the token
     *
     * @return string $token
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * Sets the token
     *
     * @param string $token
     * @return void
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * Returns the expires
     *
     * @return int $expires
     */
    public function getExpires()
    {
        return $this->expires;
    }
    
    /**
     * Sets the expires
     *
     * @param int $expires
     * @return void
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
    }
    
    /**
     * Adds a News
     *
     * @param \Socialstream\SocialStream\Domain\Model\News $news
     * @return void
     */
    public function addNews(\Socialstream\SocialStream\Domain\Model\News $news)
    {
        $this->news->attach($news);
    }
    
    /**
     * Removes a News
     *
     * @param \Socialstream\SocialStream\Domain\Model\News $newsToRemove The News to be removed
     * @return void
     */
    public function removeNews(\Socialstream\SocialStream\Domain\Model\News $newsToRemove)
    {
        $this->news->detach($newsToRemove);
    }
    
    /**
     * Returns the news
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Socialstream\SocialStream\Domain\Model\News> $news
     */
    public function getNews()
    {
        return $this->news;
    }
    
    /**
     * Sets the news
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Socialstream\SocialStream\Domain\Model\News> $news
     * @return void
     */
    public function setNews(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $news)
    {
        $this->news = $news;
    }

}