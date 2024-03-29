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
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Script Class for rendering the Table Wizard
 */
class EidNode extends AbstractNode
{
    public function render()
    {
        $rootline = GeneralUtility::makeInstance(RootlineUtility::class, $this->data['databaseRow']['pid']);
        $rootlinePages = $rootline->get();

        foreach ($rootlinePages as $level) {
            if ($level['is_siteroot']) {
                $root = $level;
            }
        }

        if (!$root) {
            array_pop($rootlinePages);
        }

        $http = isset($_SERVER['HTTPS']) ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"];
        $url = $http . "://" . $host . "/?id=" . $root["uid"] . "&eID=generate_token&channel=" . $this->data['databaseRow']['uid'];

        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $tcaView */
        $tcaView = GeneralUtility::makeInstance(StandaloneView::class);
        $tcaView->setFormat('html');
        $templatePathAndFilename = 'EXT:social_stream/Resources/Private/Backend/Templates/Channel/EidUrl.html';
        $tcaView->setTemplatePathAndFilename($templatePathAndFilename);
        $tcaView->assign('url', $url);

        $result['html'] = $tcaView->render();

        return $result;
    }

}
