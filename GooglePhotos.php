<?php
$http = isset($_SERVER['HTTPS']) ? "https" : "http";
$host = $_SERVER["HTTP_HOST"];
$request = $_SERVER["REQUEST_URI"];
$params = explode("?", $request)[1];

parse_str($params, $parts);

if (array_key_exists("state", $parts)) {
    $url = $http . "://" . $host . "/?eID=generate_token&channel=" . $parts["state"];

    if(array_key_exists("code", $parts)){
        $url .= "&code=" . $parts["code"];
    }
    header('Location: ' . $url);
}