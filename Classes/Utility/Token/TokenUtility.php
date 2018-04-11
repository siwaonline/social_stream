<?php

namespace Socialstream\SocialStream\Utility\Token;


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
 * TokenUtility
 */
class TokenUtility extends \Socialstream\SocialStream\Utility\BaseUtility
{
    /**
     * @param int $id
     * @param int $typeNum
     */
    public static function initTSFE($id = 1, $typeNum = 0)
    {
        parent::initTSFE($id, $typeNum);
    }

    /**
     * Init settings
     */
    public function initSettings()
    {
        parent::initSettings();
    }

    /**
     * @param string $type
     * @param int $pid
     * @return mixed
     */
    public static function getUtility($type, $pid = 0)
    {
        $classname = "\\Socialstream\\SocialStream\\Utility\\Token\\".ucfirst($type)."Utility";

        return new $classname($pid);
    }

}