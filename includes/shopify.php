<?php
require_once __DIR__ . '/../vendor/autoload.php';
use GuzzleHttp\Client;

class Shopify {
    private $apiKey;
    private $apiSecret;
    private $scopes;
    private $redirectUri;
    private $installRedirectUri;
    private $client;

    public function __construct() {
        $this->apiKey = $_ENV['SHOPIFY_API_KEY'];
        $this->apiSecret = $_ENV['SHOPIFY_API_SECRET'];
        $this->scopes = $_ENV['SHOPIFY_SCOPES'];
        $this->redirectUri = $_ENV['SHOPIFY_APP_REDIRECT_URI'];
        $this->installRedirectUri = $_ENV['SHOPIFY_APP_INSTALL_REDIRECT_URI'];
        $this->client = new Client();
    }

    public function installUrl($shop) {
        return "https://{$shop}/admin/oauth/authorize?client_id={$this->apiKey}&scope={$this->scopes}&redirect_uri={$this->redirectUri}";
    }

    public function verifyHmac($params) {
        $hmac = $params['hmac'] ?? '';
        unset($params['hmac']);
        
        ksort($params);
        $computedHmac = hash_hmac('sha256', http_build_query($params), $this->apiSecret);
        
        return hash_equals($hmac, $computedHmac);
    }

    public function getAccessToken($shop, $code) {
        $url = "https://{$shop}/admin/oauth/access_token";
        $response = $this->client->post($url, [
            'form_params' => [
                'client_id' => $this->apiKey,
                'client_secret' => $this->apiSecret,
                'code' => $code
            ]
        ]);
        
        return json_decode($response->getBody(), true)['access_token'];
    }
}
?>