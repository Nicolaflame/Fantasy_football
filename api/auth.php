<?php
require_once('config.php');

class YahooFantasyAPI {
    private $access_token;
    
    public function __construct() {
        session_start();
    }
    
    public function getAuthUrl() {
        $params = [
            'client_id' => YAHOO_CLIENT_ID,
            'redirect_uri' => YAHOO_REDIRECT_URI,
            'response_type' => 'code',
            'scope' => 'fspt-w'
        ];
        
        return YAHOO_OAUTH_URL . '?' . http_build_query($params);
    }
    
    public function getAccessToken($code) {
        $url = 'https://api.login.yahoo.com/oauth2/get_token';
        
        $params = [
            'client_id' => YAHOO_CLIENT_ID,
            'client_secret' => YAHOO_CLIENT_SECRET,
            'redirect_uri' => YAHOO_REDIRECT_URI,
            'code' => $code,
            'grant_type' => 'authorization_code'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($response, true);
        $_SESSION['access_token'] = $data['access_token'];
        
        return $data['access_token'];
    }
    
    public function getLeagueData() {
        if (!isset($_SESSION['access_token'])) {
            throw new Exception('Not authenticated');
        }
        
        $url = 'https://fantasysports.yahooapis.com/fantasy/v2/users;use_login=1/games;game_keys=nfl/leagues/standings?format=json';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $_SESSION['access_token']
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
}
?> 