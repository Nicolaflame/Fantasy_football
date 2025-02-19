<?php
require_once('auth.php');

$api = new YahooFantasyAPI();

if (isset($_GET['code'])) {
    $api->getAccessToken($_GET['code']);
    header('Location: ../index.html');
    exit;
}
?> 