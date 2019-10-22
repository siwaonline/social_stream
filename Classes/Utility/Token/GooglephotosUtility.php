<?php

namespace Socialstream\SocialStream\Utility\Token;


use Google\Auth\OAuth2;

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
 * YoutubeUtility
 */
class GooglephotosUtility extends \Socialstream\SocialStream\Utility\Token\TokenUtility
{
    protected $redirectUrl = '';

    public function getAccessUrl($redirect)
    {
        $url_parts = $this->splitRedirectUrl($redirect);
        $url = "https://accounts.google.com/o/oauth2/v2/auth?client_id=" . $this->settings["googlephotosclientid"] . "&scope=" . urlencode("https://www.googleapis.com/auth/photoslibrary.sharing") . "&access_type=offline&response_type=code&include_granted_scopes=true&prompt=consent&state=" . urlencode($url_parts["state"]) . "&redirect_uri=" . urlencode($url_parts["base"]);
        return $url;
    }

    public function getTokenJavascript($accessUrl, $actualUrl)
    {
        $script = '
<script>
    var hash = window.location.hash;
    if(hash == ""){
        window.location.replace("' . $accessUrl . '");
    }else{
        window.location.replace("' . $actualUrl . '&"+hash.substring(1));
    }
</script>
';
        return $script;
    }

    public function retrieveToken($url)
    {
        $parts = parse_url($url);
        parse_str($parts['query'], $params);

        if ($params["code"]) {
            if (!empty($this->redirectUrl)) {

                if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
                    $base = 'https' . '://' . $_SERVER['SERVER_NAME'];
                } else {
                    $base = 'http' . '://' . $_SERVER['SERVER_NAME'];
                }

                $oauth2 = new OAuth2([
                    'clientId' => $this->settings["googlephotosclientid"],
                    'clientSecret' => $this->settings["googlephotosclientsecret"],
                    'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
                    'redirectUri' => $base . "/typo3conf/ext/social_stream/Redirect.php",
                    'tokenCredentialUri' => 'https://www.googleapis.com/oauth2/v4/token',
                    'scope' => ['https://www.googleapis.com/auth/photoslibrary.readonly', 'https://www.googleapis.com/auth/photoslibrary.sharing'],
                    'state' => 'offline'
                ]);

                $oauth2->setCode($params["code"]);
                $authToken = $oauth2->fetchAuthToken();

                return $authToken;
            }
        }
        return false;
    }

    public function getValues($tokenString)
    {
        return array("tk" => $tokenString["access_token"], "rf_tk" => $tokenString["refresh_token"], "exp" => time() + $tokenString["expires_in"]);
    }

    /**
     * @param string $redirectUrl
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }
}