<?php

namespace Socialstream\SocialStream\Nodes;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Script Class for rendering the Table Wizard
 */
class EidNode extends AbstractNode
{
    public function render()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $tcaView */
        $tcaView = $objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $tcaView->setFormat('html');

        $http = isset($_SERVER['HTTPS']) ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"];
        $url = $http . "://" . $host . "/?eID=generate_token&channel=" . $this->data['databaseRow']['uid'];

        $templatePathAndFilename = 'EXT:social_stream/Resources/Private/Backend/Templates/Channel/EidUrl.html';
        $tcaView->setTemplatePathAndFilename($templatePathAndFilename);
        $tcaView->assign('url', $url);

        $result['html'] = $tcaView->render();

        return $result;
    }

}
