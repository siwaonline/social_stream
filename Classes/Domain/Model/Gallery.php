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
 * Gallery
 */
class Gallery extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * id
     *
     * @var string
     */
    protected $id = '';
    
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
     * createdTime
     *
     * @var \DateTime
     */
    protected $createdTime = null;
    
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