<?php

$http = isset($_SERVER['HTTPS']) ? "https" : "http";
$host = $_SERVER["HTTP_HOST"];
$request = $_SERVER["REQUEST_URI"];
$params = explode("?",$request)[1];

if($params == ""){
    $url = $http . "://" . $host . $_SERVER["REQUEST_URI"];

    echo
        '
<script>
    var hash = window.location.hash;
    if(hash == ""){
        alert("Error");
    }else{
        window.location.replace("'.$url.'"+hash.substring(1));
    }
</script>';

}else {
    parse_str($params, $parts);
    $state = $parts["state"];

    $state = urldecode($state);
    $state = str_replace(",", "&", $state);
    $state = str_replace(get_string_between($state,"&P[returnUrl]=","&P["),urlencode(get_string_between($state,"&P[returnUrl]=","&P[")),$state);

    $url = $http . "://" . $host . "/typo3/index.php?" . $state."&access_token=".$parts["access_token"]."&expires_in=".$parts["expires_in"];
    header('Location: '.$url);
}

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}