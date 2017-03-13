<?php


namespace Jasdero\PassePlatBundle\Services;

use Google_Client;
use Google_Service_Drive;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DriveConnection
 * @package Jasdero\PassePlatBundle\Services
 * This class was written following the principles of :
 * https://developers.google.com/api-client-library/php/auth/web-app
 */
class DriveConnection extends Controller
{
    /**
     * @return Google_Service_Drive|null
     */
    public function connectToDriveApi()
    {
        //initializing Client
        $client = new Google_Client();
        $client->setAuthConfig('C:\wamp64\www\order_manager\vendor\client_secret.json');
        $client->setAccessType('offline');
        $client->addScope(Google_Service_Drive::DRIVE);
        $pathToRefreshToken = 'C:\wamp64\www\order_manager\vendor\refresh_token.json';


        // getting the files if the OAuth flow has been validated
        if (isset($_SESSION['access_token']) && $_SESSION['access_token'] && !$client->isAccessTokenExpired()) {
            $client->setAccessToken($_SESSION['access_token']);
            $response = new Google_Service_Drive($client);
        } elseif ($client->isAccessTokenExpired() && file_exists($pathToRefreshToken)) {
            $refreshToken = json_decode(file_get_contents($pathToRefreshToken));
            $newToken = $client->refreshToken($refreshToken);
            $_SESSION['access_token'] = $newToken;
            $client->setAccessToken($_SESSION['access_token']);
            $response = new Google_Service_Drive($client);

        } else {
            $response = null;
        }
        return $response;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function authCheckedAction()
    {
        $client = new Google_Client();
        $client->setAuthConfigFile('C:\wamp64\www\order_manager\vendor\client_secret.json');
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/app_dev.php/checked');
        $client->setAccessType('offline');
        $client->addScope(Google_Service_Drive::DRIVE);
        $pathToRefreshToken = 'C:\wamp64\www\order_manager\vendor\refresh_token.json';


        if (!isset($_GET['code'])) {
            $auth_url = $client->createAuthUrl();
            return $this->redirect(filter_var($auth_url, FILTER_SANITIZE_URL));
        } else {
            $client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $client->getAccessToken();
            if (!file_exists($pathToRefreshToken)) {
                $refreshToken = fopen($pathToRefreshToken, 'a+');
                $jsonToken = $client->getRefreshToken();
                fwrite($refreshToken, json_encode($jsonToken));
                fclose($refreshToken);
            }
            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/app_dev.php/admin/checking';
            return $this->redirect(filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }
    }
}