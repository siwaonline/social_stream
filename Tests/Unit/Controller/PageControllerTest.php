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
 * Test case for class Socialstream\SocialStream\Controller\PageController.
 *
 */
class PageControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

	/**
	 * @var \Socialstream\SocialStream\Controller\PageController
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = $this->getMock('Socialstream\\SocialStream\\Controller\\PageController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	public function tearDown()
	{
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllPagesFromRepositoryAndAssignsThemToView()
	{

		$allPages = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$pageRepository = $this->getMock('Socialstream\\SocialStream\\Domain\\Repository\\PageRepository', array('findAll'), array(), '', FALSE);
		$pageRepository->expects($this->once())->method('findAll')->will($this->returnValue($allPages));
		$this->inject($this->subject, 'pageRepository', $pageRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('pages', $allPages);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function showActionAssignsTheGivenPageToView()
	{
		$page = new \Socialstream\SocialStream\Domain\Model\Page();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('page', $page);

		$this->subject->showAction($page);
	}

	/**
	 * @test
	 */
	public function createActionAddsTheGivenPageToPageRepository()
	{
		$page = new \Socialstream\SocialStream\Domain\Model\Page();

		$pageRepository = $this->getMock('Socialstream\\SocialStream\\Domain\\Repository\\PageRepository', array('add'), array(), '', FALSE);
		$pageRepository->expects($this->once())->method('add')->with($page);
		$this->inject($this->subject, 'pageRepository', $pageRepository);

		$this->subject->createAction($page);
	}

	/**
	 * @test
	 */
	public function editActionAssignsTheGivenPageToView()
	{
		$page = new \Socialstream\SocialStream\Domain\Model\Page();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('page', $page);

		$this->subject->editAction($page);
	}

	/**
	 * @test
	 */
	public function updateActionUpdatesTheGivenPageInPageRepository()
	{
		$page = new \Socialstream\SocialStream\Domain\Model\Page();

		$pageRepository = $this->getMock('Socialstream\\SocialStream\\Domain\\Repository\\PageRepository', array('update'), array(), '', FALSE);
		$pageRepository->expects($this->once())->method('update')->with($page);
		$this->inject($this->subject, 'pageRepository', $pageRepository);

		$this->subject->updateAction($page);
	}

	/**
	 * @test
	 */
	public function deleteActionRemovesTheGivenPageFromPageRepository()
	{
		$page = new \Socialstream\SocialStream\Domain\Model\Page();

		$pageRepository = $this->getMock('Socialstream\\SocialStream\\Domain\\Repository\\PageRepository', array('remove'), array(), '', FALSE);
		$pageRepository->expects($this->once())->method('remove')->with($page);
		$this->inject($this->subject, 'pageRepository', $pageRepository);

		$this->subject->deleteAction($page);
	}
}
