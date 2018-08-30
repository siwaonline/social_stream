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
 * Test case for class \Socialstream\SocialStream\Domain\Model\News.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class NewsTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
	/**
	 * @var \Socialstream\SocialStream\Domain\Model\News
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = new \Socialstream\SocialStream\Domain\Model\News();
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
	public function getPlaceNameReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getPlaceName()
		);
	}

	/**
	 * @test
	 */
	public function setPlaceNameForStringSetsPlaceName()
	{
		$this->subject->setPlaceName('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'placeName',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPlaceStreetReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getPlaceStreet()
		);
	}

	/**
	 * @test
	 */
	public function setPlaceStreetForStringSetsPlaceStreet()
	{
		$this->subject->setPlaceStreet('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'placeStreet',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPlaceZipReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getPlaceZip()
		);
	}

	/**
	 * @test
	 */
	public function setPlaceZipForStringSetsPlaceZip()
	{
		$this->subject->setPlaceZip('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'placeZip',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPlaceCityReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getPlaceCity()
		);
	}

	/**
	 * @test
	 */
	public function setPlaceCityForStringSetsPlaceCity()
	{
		$this->subject->setPlaceCity('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'placeCity',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPlaceCountryReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getPlaceCountry()
		);
	}

	/**
	 * @test
	 */
	public function setPlaceCountryForStringSetsPlaceCountry()
	{
		$this->subject->setPlaceCountry('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'placeCountry',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPlaceLatReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getPlaceLat()
		);
	}

	/**
	 * @test
	 */
	public function setPlaceLatForStringSetsPlaceLat()
	{
		$this->subject->setPlaceLat('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'placeLat',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPlaceLngReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getPlaceLng()
		);
	}

	/**
	 * @test
	 */
	public function setPlaceLngForStringSetsPlaceLng()
	{
		$this->subject->setPlaceLng('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'placeLng',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getChannelReturnsInitialValueForChannel()
	{
		$this->assertEquals(
			NULL,
			$this->subject->getChannel()
		);
	}

	/**
	 * @test
	 */
	public function setChannelForChannelSetsChannel()
	{
		$channelFixture = new \Socialstream\SocialStream\Domain\Model\Channel();
		$this->subject->setChannel($channelFixture);

		$this->assertAttributeEquals(
			$channelFixture,
			'channel',
			$this->subject
		);
	}
}
