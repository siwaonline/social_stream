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
     * __construct
     */
    public function __construct($pid)
    {
        $this->initTSFE($pid,0);
        $this->initSettings();
    }

    public static function initTSFE($id = 1, $typeNum = 0) {
        parent::initTSFE($id,$typeNum);
    }
    public function initSettings(){
        parent::initSettings();
    }
    public static function getUtility($type,$pid=0){
        $classname = "\\Socialstream\\SocialStream\\Utility\\Token\\".ucfirst($type)."Utility";
        return new $classname($pid);
    }
    public function splitRedirectUrl($url){
        $url_parts = explode("?",$url);
        $params = str_replace("&",",",$url_parts[1]);
        $base = explode("/typo3/",$url_parts[0]);
        return array("base"=>$base[0]."/typo3conf/ext/social_stream/Redirect.php","state"=>$params);
    }
}