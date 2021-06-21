<?php

$http = isset($_SERVER['HTTPS']) ? "https" : "http";
$host = $_SERVER["HTTP_HOST"];
$request = $_SERVER["REQUEST_URI"];

$pos = stripos($request, '?');
if($pos !== false){
    $params = substr($request, $pos+1);
}

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
    // Facebook Involve Stream
    $isInvolve = false;

    parse_str($params, $parts);
    // Only needed for the Involve Facebook Stream
    if(array_key_exists("page", $parts)){
        $parts["state"] = $parts["page"];
        unset($parts["page"]);
        $isInvolve = true;
    }


    if(!$isInvolve){
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
    }else{
//        var_dump($url);
        $cleanParams = str_replace('?api_url=' . $parts["api_url"], '', $params);
        $cleanParams = str_replace('&api_url=' . $parts["api_url"], '', $params);
        $cleanParams = str_replace(",", "&", $cleanParams);
        $cleanParams = str_replace(get_string_between($cleanParams, "&P[returnUrl]=", "&P["), urlencode(get_string_between($cleanParams, "&P[returnUrl]=", "&P[")), $cleanParams);
//        $url .= urldecode($cleanParams);

        if(array_key_exists("api_url", $parts)){
            $encoded = base64_decode($parts["api_url"]);
            parse_str(explode("?", $encoded)[1], $encodedParams);
            $token = $encodedParams["token"];

            $prefix = "&";
            if(substr($url, -1) === "?"){
                $prefix = "";
            }
            $url .= $prefix . "access_token=" . $token;
        }
    }

//    var_dump($url);
//    exit;
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
