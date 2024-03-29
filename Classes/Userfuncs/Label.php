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
    /**
     * @param $parameters
     * @param $parentObject
     */
    public function getPageLabel(&$parameters, $parentObject)
    {
        if(is_array($parameters) && key_exists('row', $parameters) && is_array($parameters['row'])){
            if(key_exists('uid', $parameters['row'])){
                $record = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord($parameters['table'], $parameters['row']['uid']);
                if(!is_array($record)){
                    $record = [];
                }
                $newTitle = key_exists('type', $record) ? ucfirst($record['type']) . " - " : "";
                if(key_exists('title', $record) && $record['title']) {
                    $newTitle .= $record['title'];
                }else{
                    $newTitle .= key_exists('object_id', $record) ? $record['object_id'] : '';
                }
                $parameters['title'] = $newTitle;
            }
        }
    }

}
