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
    if(array_key_exists("api_url", $parts)){
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

        header('Location: ' . $url);
    }else{
        $api_url = $parts["api_url"];
        unset($parts["api_url"]);

        $state = str_replace('page=', "", $params);
        $state = str_replace('&api_url=' . $api_url, "", $state);
        $url .= $state;

        if($api_url){
            $encoded = base64_decode($api_url);
            $encodedArray = explode("?", $encoded);
            parse_str($encodedArray[1], $encodedParams);
            $token = $encodedParams["token"];

            $paths = explode('/', $encodedArray[0]);
            $objectId = $paths[count($paths) - 1];

            $prefix = "&";
            if(substr($url, -1) === "?"){
                $prefix = "";
            }
            $url .= urldecode($prefix) . "access_token=" . $token . "&object_id=" . $objectId;
        }

        // header( Location: <url> ) doesn't work - results in an Logout - not quite sure why I suspect there are some cookie-issues
        // those sketchy lines below do the trick so I guess I'll keep it this way


        echo '
        <script>
            window.location.replace("' . $url . '");
        </script>';
    }
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
