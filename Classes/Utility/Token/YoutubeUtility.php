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
 * YoutubeUtility
 */
class YoutubeUtility extends \Socialstream\SocialStream\Utility\Token\TokenUtility
{
    protected $redirectUrl = '';

    public function getAccessUrl($redirect)
    {
        $url_parts = $this->splitRedirectUrl($redirect);
        $url = "https://accounts.google.com/o/oauth2/v2/auth?client_id=" . $this->settings["youtubeclientid"] . "&scope=" . urlencode("https://www.googleapis.com/auth/youtube.readonly") . "&access_type=offline&response_type=code&include_granted_scopes=true&prompt=consent&state=" . urlencode($url_parts["state"]) . "&redirect_uri=" . urlencode($url_parts["base"]);
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
                $redirectUrl_parts = $this->splitRedirectUrl($this->redirectUrl);
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://www.googleapis.com/oauth2/v4/token",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "grant_type=authorization_code&code=" . urlencode($params["code"]) . "&client_id=" . $this->settings["youtubeclientid"] . "&client_secret=" . $this->settings["youtubeclientsecret"] . "&redirect_uri=" . $redirectUrl_parts["base"],
                    CURLOPT_HTTPHEADER => array(
                        "Cache-Control: no-cache",
                        "Content-Type: application/x-www-form-urlencoded"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                    echo "cURL Error #:" . $err;
                } else {
                    $jsonResponse = json_decode($response);
                    if (!empty($jsonResponse->access_token)) {
                        return $response;
                    }

                }
            }
        }
        return false;
    }

    public function getValues($tokenString)
    {
        $tokenJson = json_decode($tokenString);
        return array("tk" => $tokenJson->access_token, "rf_tk" => $tokenJson->refresh_token, "exp" => $tokenJson->expires_in);
    }

    /**
     * @param string $redirectUrl
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }
}