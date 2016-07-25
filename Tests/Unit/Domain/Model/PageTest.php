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
 * Test case for class \Socialstream\SocialStream\Domain\Model\Page.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class PageTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
	/**
	 * @var \Socialstream\SocialStream\Domain\Model\Page
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = new \Socialstream\SocialStream\Domain\Model\Page();
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
	public function getCoverUrlReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getCoverUrl()
		);
	}

	/**
	 * @test
	 */
	public function setCoverUrlForStringSetsCoverUrl()
	{
		$this->subject->setCoverUrl('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'coverUrl',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getCoverReturnsInitialValueForFileReference()
	{
		$this->assertEquals(
			NULL,
			$this->subject->getCover()
		);
	}

	/**
	 * @test
	 */
	public function setCoverForFileReferenceSetsCover()
	{
		$fileReferenceFixture = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
		$this->subject->setCover($fileReferenceFixture);

		$this->assertAttributeEquals(
			$fileReferenceFixture,
			'cover',
			$this->subject
		);
	}
}
