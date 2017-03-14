<?php


namespace Jasdero\PassePlatBundle\Services;


use Google_Service_Drive_DriveFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DriveFolderAsStatus
 * @package Jasdero\PassePlatBundle\Services
 * Used to move files to a drive folder named after the order's status
 */
class DriveFolderAsStatus extends Controller
{
    private $drive;

    public function __construct(DriveConnection $drive)
    {
        $this->drive = $drive;
    }
    /**
     * @param $statusName
     * @param $orderId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function driveFolder($statusName, $orderId)
    {
        //initializing Client
        $drive = $this->drive->connectToDriveApi();


        // getting to work if the OAuth flow has been validated
        if ($drive) {

            //getting the id of root folder
            $pageToken = null;
            $optParamsForFolder = array(
                'pageToken' => $pageToken,
                'q' => "name contains 'b+'",
                'fields' => 'nextPageToken, files(id)'
            );

            //recovering the folder
            $results = $drive->files->listFiles($optParamsForFolder);

            $rootFolderId = '';
            foreach ($results->getFiles() as $file) {
                $rootFolderId = ($file->getId());
            }

            //checking if the folder with status name already exists
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
            //creating folder if it doesn't exist
            if (!$folderId) {
                $fileMetadata = new Google_Service_Drive_DriveFile(array(
                    'name' => $statusName,
                    'parents' => array($rootFolderId),
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

            //retrieving file corresponding to order
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
            $drive->files->update($fileId, $emptyFileMetadata, array(
                'addParents' => $folderId,
                'removeParents' => $previousParents,
                'fields' => 'id, parents'));

        } else {
            //if not authenticated restart for token
            return $this->drive->authCheckedAction();
        }
    }
}