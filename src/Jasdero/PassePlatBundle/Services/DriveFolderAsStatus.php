<?php


namespace Jasdero\PassePlatBundle\Services;


use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DriveFolderAsStatus extends Controller
{
    public function driveFolder($statusName, $orderId)
    {
        //initializing Client
        $client = new Google_Client();
        $client->setAuthConfig('C:\wamp64\www\order_manager\vendor\client_secret.json');
        $client->addScope(Google_Service_Drive::DRIVE);

        // getting to work if the OAuth flow has been validated
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
            $drive = new Google_Service_Drive($client);

            //getting the id of root folder
            $pageToken = null;

            $optParamsForFolder = array(
                'pageToken' => $pageToken,
                'q' => "name contains 'b+'",
                'fields' => 'nextPageToken, files(id)'
            );

            //recovering the folder
            $results = $drive->files->listFiles($optParamsForFolder);

            $rootId = '';
            foreach ($results->getFiles() as $file) {
                $rootId = ($file->getId());
            }

            //checking if the folder already exists
            $optParamsForFolder = array(
                'pageToken' => $pageToken,
                'q' => "name contains '$statusName'",
                'fields' => 'nextPageToken, files(id)'
            );

            $results = $drive->files->listFiles($optParamsForFolder);
            $folderId = '';
            foreach ($results->getFiles() as $file) {
                $folderId = ($file->getId());
            }
            //creating folder if doesn't exist
            if (!$folderId) {
                $fileMetadata = new Google_Service_Drive_DriveFile(array(
                    'name' => $statusName,
                    'parents' => array($rootId),
                    'mimeType' => 'application/vnd.google-apps.folder'));
                $file = $drive->files->create($fileMetadata, array(
                    'uploadType' => 'multipart',
                    'fields' => 'id'));
                $folderId = ($file->getId());
            } else {
                //if exists getting id
                foreach ($results->getFiles() as $file) {
                    $folderId = ($file->getId());
                }
            }

            //retrieving file
            $optParamsForFile = array(
                'pageToken' => $pageToken,
                'q' => "appProperties has { key = 'customID' and value = '$orderId'}",
                'fields' => 'nextPageToken, files(id)'
            );

            //recovering the file id
            $results = $drive->files->listFiles($optParamsForFile);

            $fileId = '';
            foreach ($results->getFiles() as $file) {
                $fileId = ($file->getId());
            }
            //moving file
            $emptyFileMetadata = new Google_Service_Drive_DriveFile();
            // Retrieve the existing parents to remove
            $file = $drive->files->get($fileId, array('fields' => 'parents'));
            $previousParents = join(',', $file->parents);

            // Move the file to the new folder
            $file = $drive->files->update($fileId, $emptyFileMetadata, array(
                'addParents' => $folderId,
                'removeParents' => $previousParents,
                'fields' => 'id, parents'));

        } else {
            //if not authenticated restart for token
            $auth_url = $client->createAuthUrl();
            return $this->redirect(filter_var($auth_url, FILTER_SANITIZE_URL));
        }
    }
}