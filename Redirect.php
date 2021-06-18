<?php

$http = isset($_SERVER['HTTPS']) ? "https" : "http";
$host = $_SERVER["HTTP_HOST"];
$request = $_SERVER["REQUEST_URI"];
$params = explode("?", $request)[1];

if ($params == "") {
    $url = $http . "://" . $host . $_SERVER["REQUEST_URI"];

    echo
        '
<script>
    var hash = window.location.hash;
    if(hash == ""){
        alert("Error");
    }else{
        var uri = "' . $url . '"+hash.substring(1);
        window.location.replace(uri);
    }
</script>';

} else {
    $url = $http . "://" . $host . "/typo3/index.php?";

    parse_str($params, $parts);

    if (array_key_exists("state", $parts)) {
        $state = $parts["state"];
        $state = urldecode($state);
        $state = str_replace(",", "&", $state);
        $state = str_replace(get_string_between($state, "&P[returnUrl]=", "&P["), urlencode(get_string_between($state, "&P[returnUrl]=", "&P[")), $state);
        $url .= $state;
    }
    if (array_key_exists("code", $parts)) {
        $url .= "&code=" . $parts["code"];
    }
    if (array_key_exists("access_token", $parts)) {
        $url .= "&access_token=" . $parts["access_token"];
    }
    if (array_key_exists("expires_in", $parts)) {
        $url .= "&expires_in=" . $parts["expires_in"];
    }

    // Facebook Involve Stream
    if(array_key_exists("api_url", $parts)){
        $encoded = base64_decode($parts["api_url"]);
        parse_str(explode("?", $request)[1], $encodedParams);
        $token = $encodedParams["api_url"];

        $prefix = "&";
        if(substr($url, -1) === "?"){
            $prefix = "";
        }
        $url .= $prefix . "access_token=" . $token;
    }

    header('Location: ' . $url);
}

function get_string_between($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
