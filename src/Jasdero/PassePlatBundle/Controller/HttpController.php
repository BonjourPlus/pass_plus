<?php

namespace Jasdero\PassePlatBundle\Controller;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class HttpController extends CheckingController
{

    //try to read from google drive sheet
    /**
     *
     * @Route("/checking", name="checking")
     * @Method({"GET", "POST"})
     */

    public function driveSheetAction()
    {
        //initializing Client
        $client = new Google_Client();
        $client->setAuthConfig('C:\wamp64\www\order_manager\vendor\client_secret.json');
        $client->addScope(Google_Service_Drive::DRIVE);

        // getting the files if the OAuth flow has been validated
       /* if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);*/
            $drive = $this->get('driveconnection')->connectToDriveApi();
            if($drive){
            $pageToken = null;

            //scanning the new orders folder to create new orders, then moving it to the In progress folder
            //options to get the new Orders folder on the drive
            $optParamsForFolder = array(
                'pageToken' => $pageToken,
                'q' => "name contains 'NewOrders'",
                'fields' => 'nextPageToken, files(id)'
            );

            //recovering the folder
            $results = $drive->files->listFiles($optParamsForFolder);

            $folderId = '';
            foreach ($results->getFiles() as $file) {
                $folderId = ($file->getId());
            }

            //options to get the Orders inside the folder
            $optParamsForFiles = array(
                'pageToken' => $pageToken,
                'q' => "'$folderId' in parents",
                'fields' => 'nextPageToken, files(id)'
            );

            //recovering the files
            $results = $drive->files->listFiles($optParamsForFiles);

            $files = [];
            foreach ($results->getFiles() as $file) {
                $files[] = ($file->getId());

                if ($files) {
                    //downloading files in a csv format and turning it into associative arrays
                    $csvFiles = [];
                    foreach ($files as $file) {
                        $response = $drive->files->export($file, 'text/csv', array(
                            'alt' => 'media',
                        ));
                        $newFile = fopen('order.csv', 'w+');
                        fwrite($newFile, $response->getBody()->getContents());
                        fclose($newFile);

                        //method  to turn csv into arrays
                        $csv = array_map('str_getcsv', file('order.csv'));
                        array_walk($csv, function (&$a) use ($csv) {
                            $a = array_combine($csv[0], $a);
                        });
                        array_shift($csv);
                        $csvFiles[] = $csv;
                    }

                    //formatting csv files to proper order format
                    $newOrders = $this->csvToOrders($csvFiles);

                    //array to store custom Ids which will be added to files later
                    $ordersIds = [];
                    //creating new orders
                    foreach ($newOrders as $newOrder) {
                        if ($user = $this->validateUser($newOrder['user'])) {
                            $ordersIds[] = $this->forward('JasderoPassePlatBundle:Orders:new', array(
                                'user' => $user,
                                'products' => $newOrder['products'],
                            ))->getContent();
                        }
                    }

                    //moving to 'in progress folder'
                    //options to get the In Progress folder on the drive
                    $optParamsForFolder = array(
                        'pageToken' => $pageToken,
                        'q' => "name contains 'InProgress'",
                        'fields' => 'nextPageToken, files(id)'
                    );

                    //recovering the folder id
                    $results = $drive->files->listFiles($optParamsForFolder);

                    $folderId = '';
                    foreach ($results->getFiles() as $file) {
                        $folderId = ($file->getId());
                    }

                    //moving files
                    foreach ($files as $key => $fileId) {
                        //adding the custom order id as additional property
                        $extraFileMetadata = new Google_Service_Drive_DriveFile(array(
                            "appProperties" => [
                                "customID" => $ordersIds[$key],
                            ]
                        ));

                        // Retrieve the existing parents to remove
                        $file = $drive->files->get($fileId, array('fields' => 'parents'));
                        $previousParents = join(',', $file->parents);

                        // Move the file to the new folder
                        $file = $drive->files->update($fileId, $extraFileMetadata, array(
                            'addParents' => $folderId,
                            'removeParents' => $previousParents,
                            'fields' => 'id, parents, appProperties'));
                    }
                }
            }

            return New Response();
        } else {

            //if not authenticated restart for token
            return $this->redirectToRoute('auth_checked');

        }
    }

    //redirection page, used in the OAuth2 authentication Flow
    /**
     * @Route("/checked", name="auth_checked")
     *
     */
    public function authCheckedAction()
    {
        return $this->get('driveconnection')->authCheckedAction();

    }


    /**
     * used to turn csv files into proper format for new orders
     * @param array $orders
     * @return array
     */
    private function csvToOrders(array $orders)
    {
        $formattedOrders = [];

        foreach ($orders as $order) {
            $formattedOrder = array(
                'user' => '',
                'products' => '',
            );
            foreach ($order as $array) {
                if ($array['user'] != null) {
                    $formattedOrder['user'] = $array['user'];
                }
                if ($array['products'] != null) {
                    $formattedOrder['products'][] = (int)$array['products'];
                }
            }
            $formattedOrders[] = $formattedOrder;
        }
        return ($formattedOrders);
    }
}