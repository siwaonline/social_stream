<?php
namespace Socialstream\SocialStream\Tests\Unit\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 
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
 * Test case for class Socialstream\SocialStream\Controller\GalleryController.
 *
 */
class GalleryControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

	/**
	 * @var \Socialstream\SocialStream\Controller\GalleryController
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = $this->getMock('Socialstream\\SocialStream\\Controller\\GalleryController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	public function tearDown()
	{
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllGalleriesFromRepositoryAndAssignsThemToView()
	{

		$allGalleries = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$galleryRepository = $this->getMock('Socialstream\\SocialStream\\Domain\\Repository\\GalleryRepository', array('findAll'), array(), '', FALSE);
		$galleryRepository->expects($this->once())->method('findAll')->will($this->returnValue($allGalleries));
		$this->inject($this->subject, 'galleryRepository', $galleryRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('galleries', $allGalleries);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function showActionAssignsTheGivenGalleryToView()
	{
		$gallery = new \Socialstream\SocialStream\Domain\Model\Gallery();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('gallery', $gallery);

		$this->subject->showAction($gallery);
	}

	/**
	 * @test
	 */
	public function createActionAddsTheGivenGalleryToGalleryRepository()
	{
		$gallery = new \Socialstream\SocialStream\Domain\Model\Gallery();

		$galleryRepository = $this->getMock('Socialstream\\SocialStream\\Domain\\Repository\\GalleryRepository', array('add'), array(), '', FALSE);
		$galleryRepository->expects($this->once())->method('add')->with($gallery);
		$this->inject($this->subject, 'galleryRepository', $galleryRepository);

		$this->subject->createAction($gallery);
	}

	/**
	 * @test
	 */
	public function editActionAssignsTheGivenGalleryToView()
	{
		$gallery = new \Socialstream\SocialStream\Domain\Model\Gallery();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('gallery', $gallery);

		$this->subject->editAction($gallery);
	}

	/**
	 * @test
	 */
	public function updateActionUpdatesTheGivenGalleryInGalleryRepository()
	{
		$gallery = new \Socialstream\SocialStream\Domain\Model\Gallery();

		$galleryRepository = $this->getMock('Socialstream\\SocialStream\\Domain\\Repository\\GalleryRepository', array('update'), array(), '', FALSE);
		$galleryRepository->expects($this->once())->method('update')->with($gallery);
		$this->inject($this->subject, 'galleryRepository', $galleryRepository);

		$this->subject->updateAction($gallery);
	}

	/**
	 * @test
	 */
	public function deleteActionRemovesTheGivenGalleryFromGalleryRepository()
	{
		$gallery = new \Socialstream\SocialStream\Domain\Model\Gallery();

		$galleryRepository = $this->getMock('Socialstream\\SocialStream\\Domain\\Repository\\GalleryRepository', array('remove'), array(), '', FALSE);
		$galleryRepository->expects($this->once())->method('remove')->with($gallery);
		$this->inject($this->subject, 'galleryRepository', $galleryRepository);

		$this->subject->deleteAction($gallery);
	}
}
