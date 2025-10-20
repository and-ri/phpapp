<?php

use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Google\Client as GoogleClient;

class google_auth {
    private $client;
    private $registry;

    public function __construct($registry) {
        $this->registry = $registry;
    }   

    public function __get($key) {
        return $this->registry->get($key);
    }

    public function init() {
        $this->client = new GoogleClient();
        $this->client->setClientId($this->env->get('GOOGLE_AUTH_CLIENT_ID'));
        $this->client->setClientSecret($this->env->get('GOOGLE_AUTH_CLIENT_SECRET'));
        $this->client->setRedirectUri($this->env->get('GOOGLE_AUTH_REDIRECT_URI'));
        $this->client->addScope('email');
        $this->client->addScope('profile');
    }

    public function getAuthUrl() {
        return $this->client->createAuthUrl();
    }

    public function authenticate($code) {
        $this->client->authenticate($code);
        $_SESSION['access_token'] = $this->client->getAccessToken();
    }

    public function getUserInfo() {
        if (!isset($_SESSION['access_token'])) {
            throw new Exception('User not authenticated');
        }

        $this->client->setAccessToken($_SESSION['access_token']);
        $oauth2 = new \Google_Service_Oauth2($this->client);
        return $oauth2->userinfo->get();
    }
}