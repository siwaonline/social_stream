<?php
namespace Socialstream\SocialStream\Utility\Token;

use Socialstream\SocialStream\Domain\Model\Channel;

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
 * FacebookUtility
 */
class BaseInvolveUtility extends \Socialstream\SocialStream\Utility\Token\TokenUtility
{
    public function getAccessUrl($redirect, $objectId)
    {
        $url_parts = $this->splitRedirectUrl($redirect);
        $url_parts["state"] = str_replace(",","&",$url_parts["state"]);

        $callback_url = $this->settings['involveAPIUrl'] . '/login/' . $this->settings['involveAppId'] . '?callback_url=' . urlencode($url_parts["base"] . '?page=' . urlencode($url_parts["state"]));
        return $callback_url;
    }
    public function getTokenJavascript($accessUrl,$actualUrl){

        $script = '
<script>
    var hash = window.location.hash;
    if(hash == ""){
        window.location.replace("'.$accessUrl.'");
    }else{
        window.location.replace("'.$actualUrl.'&"+hash.substring(1));
    }
</script>
';
        return $script;
    }

    public function retrieveToken($url){
        $parts = parse_url($url);
        parse_str($parts['query'], $params);

        if(!key_exists('access_token', $params) || !$params["access_token"]){
            return false;
        }else{
            return $params["access_token"];
        }
    }

    public function retrieveObjectId($url){
        $parts = parse_url($url);
        parse_str($parts['query'], $params);

        if(!key_exists('object_id', $params) || !$params["object_id"]){
            return false;
        }else{
            return $params["object_id"];
        }
    }

    public function getValues($tokenString){
        return array("tk" => $tokenString, "exp" => time() + $json->expires_in);
    }

}

