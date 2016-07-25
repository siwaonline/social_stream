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
 * Test case for class \Socialstream\SocialStream\Domain\Model\Post.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class PostTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
	/**
	 * @var \Socialstream\SocialStream\Domain\Model\Post
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = new \Socialstream\SocialStream\Domain\Model\Post();
	}

	public function tearDown()
	{
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getIdReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getId()
		);
	}

	/**
	 * @test
	 */
	public function setIdForStringSetsId()
	{
		$this->subject->setId('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'id',
			$this->subject
		);
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
	public function getCreatedTimeReturnsInitialValueForDateTime()
	{
		$this->assertEquals(
			NULL,
			$this->subject->getCreatedTime()
		);
	}

	/**
	 * @test
	 */
	public function setCreatedTimeForDateTimeSetsCreatedTime()
	{
		$dateTimeFixture = new \DateTime();
		$this->subject->setCreatedTime($dateTimeFixture);

		$this->assertAttributeEquals(
			$dateTimeFixture,
			'createdTime',
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
	public function getTypeReturnsInitialValueForInt()
	{	}

	/**
	 * @test
	 */
	public function setTypeForIntSetsType()
	{	}

	/**
	 * @test
	 */
	public function getNameReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getName()
		);
	}

	/**
	 * @test
	 */
	public function setNameForStringSetsName()
	{
		$this->subject->setName('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'name',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getCaptionReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getCaption()
		);
	}

	/**
	 * @test
	 */
	public function setCaptionForStringSetsCaption()
	{
		$this->subject->setCaption('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'caption',
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
	public function getMessageReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getMessage()
		);
	}

	/**
	 * @test
	 */
	public function setMessageForStringSetsMessage()
	{
		$this->subject->setMessage('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'message',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPictureUrlReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getPictureUrl()
		);
	}

	/**
	 * @test
	 */
	public function setPictureUrlForStringSetsPictureUrl()
	{
		$this->subject->setPictureUrl('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'pictureUrl',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPictureReturnsInitialValueForFileReference()
	{
		$this->assertEquals(
			NULL,
			$this->subject->getPicture()
		);
	}

	/**
	 * @test
	 */
	public function setPictureForFileReferenceSetsPicture()
	{
		$fileReferenceFixture = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
		$this->subject->setPicture($fileReferenceFixture);

		$this->assertAttributeEquals(
			$fileReferenceFixture,
			'picture',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPageReturnsInitialValueForPage()
	{
		$this->assertEquals(
			NULL,
			$this->subject->getPage()
		);
	}

	/**
	 * @test
	 */
	public function setPageForPageSetsPage()
	{
		$pageFixture = new \Socialstream\SocialStream\Domain\Model\Page();
		$this->subject->setPage($pageFixture);

		$this->assertAttributeEquals(
			$pageFixture,
			'page',
			$this->subject
		);
	}
}
