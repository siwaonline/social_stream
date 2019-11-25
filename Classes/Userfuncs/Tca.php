<?php

namespace Socialstream\SocialStream\Userfuncs;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2019
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
 * Label
 */
class Tca
{
    /**
     * @param $parameters
     * @param $parentObject
     * @return string
     */
    public function getEidUrl(&$parameters, $parentObject)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $page = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Page\\PageRepository');
        $uriBuilder = $objectManager->get(UriBuilder::class);

        $rootLine = $page->getRootLine($parameters['row']['pid']);

        foreach ($rootLine as $level) {
            if ($level['is_siteroot']) {
                $root = $level;
            }
        }

        if (!$root) {
            array_pop($rootLine);
        }

        $url = $uriBuilder->reset()->setArguments(['eID' => 'generate_token', 'channel' => $parameters['row']['uid']])->setTargetPageUid($root['uid'])->setUseCacheHash(FALSE)->setCreateAbsoluteUri(TRUE)->buildFrontendUri();

        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $tcaView */
        $tcaView = $objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $tcaView->setFormat('html');
        $templatePathAndFilename = 'EXT:social_stream/Resources/Private/Backend/Templates/Channel/EidUrl.html';
        $tcaView->setTemplatePathAndFilename($templatePathAndFilename);
        $tcaView->assign('url', $url);
        return $tcaView->render();
    }

}