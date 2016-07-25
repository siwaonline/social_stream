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
 * Page
 */
class Page extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * id
     *
     * @var string
     */
    protected $id = '';

    /**
     * me
     *
     * @var int
     */
    protected $me = 0;

    /**
     * streamtype
     *
     * @var int
     */
    protected $streamtype = 0;

    /**
     * token
     *
     * @var string
     */
    protected $token = '';

    /**
     * tokensecret
     *
     * @var string
     */
    protected $tokensecret = '';

    /**
     * expires
     *
     * @var integer
     */
    protected $expires = 0;
    
    /**
     * name
     *
     * @var string
     */
    protected $name = '';
    
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
     * link
     *
     * @var string
     */
    protected $link = '';
    
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
     * coverUrl
     *
     * @var string
     */
    protected $coverUrl = '';
    
    /**
     * cover
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $cover = null;
    
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
     * @return int
     */
    public function getMe()
    {
        return $this->me;
    }

    /**
     * @param int $me
     */
    public function setMe($me)
    {
        $this->me = $me;
    }

    /**
     * @return int
     */
    public function getStreamtype()
    {
        return $this->streamtype;
    }

    /**
     * @param int $streamtype
     */
    public function setStreamtype($streamtype)
    {
        $this->streamtype = $streamtype;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getTokensecret()
    {
        return $this->tokensecret;
    }

    /**
     * @param string $tokensecret
     */
    public function setTokensecret($tokensecret)
    {
        $this->tokensecret = $tokensecret;
    }
    
    /**
     * @return int
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @param int $expires
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
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
     * Returns the coverUrl
     *
     * @return string $coverUrl
     */
    public function getCoverUrl()
    {
        return $this->coverUrl;
    }
    
    /**
     * Sets the coverUrl
     *
     * @param string $coverUrl
     * @return void
     */
    public function setCoverUrl($coverUrl)
    {
        $this->coverUrl = $coverUrl;
    }
    
    /**
     * Returns the cover
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $cover
     */
    public function getCover()
    {
        return $this->cover;
    }
    
    /**
     * Sets the cover
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $cover
     * @return void
     */
    public function setCover(\TYPO3\CMS\Extbase\Domain\Model\FileReference $cover)
    {
        $this->cover = $cover;
    }

    /**
     * @return array
     */
    public function getPosts(){
        $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery("*","tx_socialstream_domain_model_post","hidden = 0 AND deleted = 0 AND page=".$this->getUid(),"","","");
        $posts = array();
        while($result = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)){
            array_push($posts,$result);
        }
        return $posts;
    }

    /**
     * @return array
     */
    public function getGalleries(){
        $query = $GLOBALS['TYPO3_DB']->exec_SELECTquery("*","tx_socialstream_domain_model_gallery","hidden = 0 AND deleted = 0 AND page=".$this->getUid(),"","","");
        $galleries = array();
        while($result = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)){
            array_push($galleries,$result);
        }
        return $galleries;
    }

}