<?php
namespace Socialstream\SocialStream\Userfuncs;


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
 * Label
 */
class Label
{
    public function getLabel(&$parameters, $parentObject)
    {
        $record = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord($parameters['table'], $parameters['row']['uid']);
        $newTitle = $record['name'];
        if($record['metatitle']) {
            $newTitle .= " (";
            $newTitle .= $record['metatitle'];
            $newTitle .= ")";
        }
        $parameters['title'] = $newTitle;
    }

    public function getStreamtype(&$parameters, $parentObject)
    {
        $record = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord($parameters['table'], $parameters['row']['uid']);
        $newTitle = "not defined";
        if($record['streamtype'] == 1)$newTitle = "Facebook";
        if($record['streamtype'] == 2)$newTitle = "Instagram";
        if($record['streamtype'] == 3)$newTitle = "YouTube";
        if($record['streamtype'] == 4)$newTitle = "Twitter";
        if($record['streamtype'] == 5)$newTitle = "Xing";
        if($record['streamtype'] == 6)$newTitle = "LinkedIn";
        $parameters['title'] = $newTitle;
    }

    public function getPageLabel(&$parameters, $parentObject)
    {
        $record = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord($parameters['table'], $parameters['row']['uid']);
        $newTitle = "";
        if($record['streamtype'] == 1)$newTitle = "Facebook";
        if($record['streamtype'] == 2)$newTitle = "Instagram";
        if($record['streamtype'] == 3)$newTitle = "YouTube";
        if($record['streamtype'] == 4)$newTitle = "Twitter";
        if($record['streamtype'] == 5)$newTitle = "Xing";
        if($record['streamtype'] == 6)$newTitle = "LinkedIn";
        $newTitle .= ": ".$record['name'];
        $parameters['title'] = $newTitle;
    }
}