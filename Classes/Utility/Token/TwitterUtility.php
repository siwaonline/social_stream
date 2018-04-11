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
 * TwitterUtility
 */
class TwitterUtility extends \Socialstream\SocialStream\Utility\Token\TokenUtility
{
    public function getAccessUrl($redirect)
    {
        return $redirect;
    }
    public function getTokenJavascript($accessUrl,$actualUrl){
        return "should not be called";
    }

    public function retrieveToken($url){
        $encodedToken = base64_encode(urlencode($this->settings["twappid"]) . ":" . urlencode($this->settings["twappsecret"]));

        $ch = curl_init("https://api.twitter.com/oauth2/token?grant_type=client_credentials");
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Basic '. $encodedToken
        );

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30
        );

        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);
        $bearer = json_decode($result,true);


        return $bearer["access_token"];
    }

    public function getValues($tokenString){
        return array("tk" => $tokenString, "exp" => 0);
    }

}