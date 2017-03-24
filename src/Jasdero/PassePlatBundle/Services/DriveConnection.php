<?php


namespace Jasdero\PassePlatBundle\Services;

use Google_Client;
use Google_Service_Drive;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class DriveConnection
 * @package Jasdero\PassePlatBundle\Services
 * This class was written following the principles of :
 * https://developers.google.com/api-client-library/php/auth/web-app
 */
class DriveConnection
{

    private $authConfig;
    private $pathToRefreshToken;

    /**
     * DriveConnection constructor.
     * @param $authConfig
     * @param $pathToRefreshToken
     */
    public function __construct($authConfig, $pathToRefreshToken)
    {
        $this->authConfig = $authConfig;
        $this->pathToRefreshToken = $pathToRefreshToken;
    }

    /**
     * @return Google_Service_Drive|null
     */
    public function connectToDriveApi()
    {
        //initializing Client
        $client = new Google_Client();
        $client->setAuthConfigFile($this->authConfig);
        $client->setAccessType('offline');
        $client->addScope(Google_Service_Drive::DRIVE);


        // getting the files if the OAuth flow has been validated
        if (isset($_SESSION['access_token']) && $_SESSION['access_token'] && !$client->isAccessTokenExpired()) {
            $client->setAccessToken($_SESSION['access_token']);
            $response = new Google_Service_Drive($client);
        } elseif ($client->isAccessTokenExpired() && file_exists($this->pathToRefreshToken)) {
            $refreshToken = json_decode(file_get_contents($this->pathToRefreshToken));
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
        $client->setAuthConfigFile($this->authConfig);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/app_dev.php/checked');
        $client->setAccessType('offline');
        $client->addScope(Google_Service_Drive::DRIVE);


        if (!isset($_GET['code'])) {
            $auth_url = $client->createAuthUrl();
            return new RedirectResponse(filter_var($auth_url, FILTER_SANITIZE_URL));
        } else {
            $client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $client->getAccessToken();
            if (!file_exists($this->pathToRefreshToken)) {
                $refreshToken = fopen($this->pathToRefreshToken, 'a+');
                $jsonToken = $client->getRefreshToken();
                fwrite($refreshToken, json_encode($jsonToken));
                fclose($refreshToken);
            }
            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/app_dev.php/admin/checking';
            return new RedirectResponse(filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }
    }
}