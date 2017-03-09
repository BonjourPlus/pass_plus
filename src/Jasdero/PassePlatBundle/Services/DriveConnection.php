<?php
/**
 * Created by PhpStorm.
 * User: apside
 * Date: 08/03/2017
 * Time: 15:08
 */

namespace Jasdero\PassePlatBundle\Services;


use Google_Client;
use Google_Service_Drive;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DriveConnection extends Controller
{
    public function connectToDriveApi()
    {
        //initializing Client
        $client = new Google_Client();
        $client->setAuthConfig('C:\wamp64\www\order_manager\vendor\client_secret.json');
        $client->addScope(Google_Service_Drive::DRIVE);

        // getting the files if the OAuth flow has been validated
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
            $response = new Google_Service_Drive($client);
        } else {
            $response = null;
        }
        return $response;
    }

    public function authCheckedAction()
    {
        $client = new Google_Client();
        $client->setAuthConfigFile('C:\wamp64\www\order_manager\vendor\client_secret.json');
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/app_dev.php/checked');
        $client->addScope(Google_Service_Drive::DRIVE);

        if (!isset($_GET['code'])) {
            $auth_url = $client->createAuthUrl();
            return $this->redirect(filter_var($auth_url, FILTER_SANITIZE_URL));
        } else {
            $client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $client->getAccessToken();
            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/app_dev.php/';
            return $this->redirect(filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }
    }
}