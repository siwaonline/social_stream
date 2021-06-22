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
 * FacebookUtility
 */
class FacebookinvolveUtility extends \Socialstream\SocialStream\Utility\Token\TokenUtility
{
    public function getAccessUrl($redirect)
    {
        $url_parts = $this->splitRedirectUrl($redirect);

        $callback_url = 'https://stage4.involve.at/login/3?callback_url=' . urlencode($url_parts["base"] . '?page=' . str_replace(",","&", $url_parts["state"]));
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
//        var_dump("retrieve");
//        var_dump($url);
//        exit;
        $parts = parse_url($url);
        parse_str($parts['query'], $params);

        if(!$params["access_token"]){
            return false;
        }else{
            $token = file_get_contents("https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id=" . $this->settings["fbappid"] . "&client_secret=" . $this->settings["fbappsecret"] . "&fb_exchange_token=" . $params["access_token"]);
            return $token;
        }
    }

    public function getValues($tokenString){
        $json = json_decode($tokenString);
        return array("tk" => $json->access_token, "exp" => time() + $json->expires_in);
    }

}
