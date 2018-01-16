<?php

namespace Socialstream\SocialStream\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class \Socialstream\SocialStream\Domain\Model\Channel.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class ChannelTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
	/**
	 * @var \Socialstream\SocialStream\Domain\Model\Channel
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = new \Socialstream\SocialStream\Domain\Model\Channel();
	}

	public function tearDown()
	{
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getObjectIdReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getObjectId()
		);
	}

	/**
	 * @test
	 */
	public function setObjectIdForStringSetsObjectId()
	{
		$this->subject->setObjectId('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'objectId',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getTitleReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getTitle()
		);
	}

	/**
	 * @test
	 */
	public function setTitleForStringSetsTitle()
	{
		$this->subject->setTitle('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'title',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getAboutReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getAbout()
		);
	}

	/**
	 * @test
	 */
	public function setAboutForStringSetsAbout()
	{
		$this->subject->setAbout('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'about',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getDescriptionReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getDescription()
		);
	}

	/**
	 * @test
	 */
	public function setDescriptionForStringSetsDescription()
	{
		$this->subject->setDescription('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'description',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getUserReturnsInitialValueForBool()
	{
		$this->assertSame(
			FALSE,
			$this->subject->getUser()
		);
	}

	/**
	 * @test
	 */
	public function setUserForBoolSetsUser()
	{
		$this->subject->setUser(TRUE);

		$this->assertAttributeEquals(
			TRUE,
			'user',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getTypeReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getType()
		);
	}

	/**
	 * @test
	 */
	public function setTypeForStringSetsType()
	{
		$this->subject->setType('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'type',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getLinkReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getLink()
		);
	}

	/**
	 * @test
	 */
	public function setLinkForStringSetsLink()
	{
		$this->subject->setLink('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'link',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getImageReturnsInitialValueForFileReference()
	{
		$this->assertEquals(
			NULL,
			$this->subject->getImage()
		);
	}

	/**
	 * @test
	 */
	public function setImageForFileReferenceSetsImage()
	{
		$fileReferenceFixture = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
		$this->subject->setImage($fileReferenceFixture);

		$this->assertAttributeEquals(
			$fileReferenceFixture,
			'image',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getTokenReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getToken()
		);
	}

	/**
	 * @test
	 */
	public function setTokenForStringSetsToken()
	{
		$this->subject->setToken('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'token',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getExpiresReturnsInitialValueForInt()
	{	}

	/**
	 * @test
	 */
	public function setExpiresForIntSetsExpires()
	{	}

	/**
	 * @test
	 */
	public function getNewsReturnsInitialValueForNews()
	{
		$newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->subject->getNews()
		);
	}

	/**
	 * @test
	 */
	public function setNewsForObjectStorageContainingNewsSetsNews()
	{
		$news = new \Socialstream\SocialStream\Domain\Model\News();
		$objectStorageHoldingExactlyOneNews = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$objectStorageHoldingExactlyOneNews->attach($news);
		$this->subject->setNews($objectStorageHoldingExactlyOneNews);

		$this->assertAttributeEquals(
			$objectStorageHoldingExactlyOneNews,
			'news',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function addNewsToObjectStorageHoldingNews()
	{
		$news = new \Socialstream\SocialStream\Domain\Model\News();
		$newsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('attach'), array(), '', FALSE);
		$newsObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($news));
		$this->inject($this->subject, 'news', $newsObjectStorageMock);

		$this->subject->addNews($news);
	}

	/**
	 * @test
	 */
	public function removeNewsFromObjectStorageHoldingNews()
	{
		$news = new \Socialstream\SocialStream\Domain\Model\News();
		$newsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('detach'), array(), '', FALSE);
		$newsObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($news));
		$this->inject($this->subject, 'news', $newsObjectStorageMock);

		$this->subject->removeNews($news);

	}
}
