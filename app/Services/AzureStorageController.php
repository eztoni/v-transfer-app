<?php

namespace App\Services;

class AzureStorageController{

    const ACCESS_KEY = 'S1X7GtBXx9m/KME0OouUAp68U3uHhO2NSt3Lq/Z1YaVpgNA93sgw6ZE1syNAxnwOVn3DEiLujyN2+AStC152VA==';
    const STORAGE_ACCOUNT = 'saeuweprodtransfers01';

    const CONTAINER_NAME = 'document-archive';

    function uploadBlob($filetoUpload, $blobName) {

        $destinationURL = "https://".self::STORAGE_ACCOUNT.".blob.core.windows.net/".self::CONTAINER_NAME."/".$blobName;

        $currentDate = gmdate("D, d M Y H:i:s T", time());
        $handle = fopen($filetoUpload, "r");
        $fileLen = filesize($filetoUpload);

        $headerResource = "x-ms-blob-cache-control:max-age=3600\nx-ms-blob-type:BlockBlob\nx-ms-date:$currentDate\nx-ms-version:2015-12-11";
        $urlResource = "/".self::STORAGE_ACCOUNT."/".self::CONTAINER_NAME."/$blobName";

        $arraysign = array();
        $arraysign[] = 'PUT';               /*HTTP Verb*/
        $arraysign[] = '';                  /*Content-Encoding*/
        $arraysign[] = '';                  /*Content-Language*/
        $arraysign[] = $fileLen;            /*Content-Length (include value when zero)*/
        $arraysign[] = '';                  /*Content-MD5*/
        $arraysign[] = 'application/pdf';         /*Content-Type*/
        $arraysign[] = '';                  /*Date*/
        $arraysign[] = '';                  /*If-Modified-Since */
        $arraysign[] = '';                  /*If-Match*/
        $arraysign[] = '';                  /*If-None-Match*/
        $arraysign[] = '';                  /*If-Unmodified-Since*/
        $arraysign[] = '';                  /*Range*/
        $arraysign[] = $headerResource;     /*CanonicalizedHeaders*/
        $arraysign[] = $urlResource;        /*CanonicalizedResource*/

        $str2sign = implode("\n", $arraysign);

        $sig = base64_encode(hash_hmac('sha256', urldecode(utf8_encode($str2sign)), base64_decode(self::ACCESS_KEY), true));
        $authHeader = "SharedKey ".self::STORAGE_ACCOUNT.":$sig";

        $headers = [
            'Authorization: ' . $authHeader,
            'x-ms-blob-cache-control: max-age=3600',
            'x-ms-blob-type: BlockBlob',
            'x-ms-date: ' . $currentDate,
            'x-ms-version: 2015-12-11',
            'Content-Type: application/pdf',
            'Content-Length: ' . $fileLen
        ];

        $ch = curl_init($destinationURL);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_INFILE, $handle);
        curl_setopt($ch, CURLOPT_INFILESIZE, $fileLen);
        curl_setopt($ch, CURLOPT_UPLOAD, true);

        $result = curl_exec($ch);

        if(empty(curl_error($ch))){
            return 1;
        }

        return 0;

        curl_close($ch);
    }

    public function getBlobs(){

        $destinationURL = "https://".self::STORAGE_ACCOUNT.".blob.core.windows.net/".self::CONTAINER_NAME."?restype=container&comp=list";

        $currentDate = gmdate("D, d M Y H:i:s T", time());

        $headerResource = "x-ms-blob-cache-control:max-age=3600\nx-ms-blob-type:BlockBlob\nx-ms-date:$currentDate\nx-ms-version:2015-12-11";
        $urlResource = "/".self::STORAGE_ACCOUNT."/".self::CONTAINER_NAME."?restype=container&comp=list";

        $arraysign = array();
        $arraysign[] = 'GET';               /*HTTP Verb*/
        $arraysign[] = '';                  /*Content-Encoding*/
        $arraysign[] = '';                  /*Content-Language*/
        $arraysign[] = '';                  /*Content-MD5*/
        $arraysign[] = '';                  /*Date*/
        $arraysign[] = '';                  /*If-Modified-Since */
        $arraysign[] = '';                  /*If-Match*/
        $arraysign[] = '';                  /*If-None-Match*/
        $arraysign[] = '';                  /*If-Unmodified-Since*/
        $arraysign[] = '';                  /*Range*/
        $arraysign[] = $headerResource;     /*CanonicalizedHeaders*/
        $arraysign[] = $urlResource;        /*CanonicalizedResource*/

        $str2sign = implode("\n", $arraysign);

        $sig = base64_encode(hash_hmac('sha256', urldecode(utf8_encode($str2sign)), base64_decode(self::ACCESS_KEY), true));
        $authHeader = "SharedKey ".self::STORAGE_ACCOUNT.":$sig";

        $headers = [
            'Authorization: ' . $authHeader,
            'x-ms-blob-cache-control: max-age=3600',
            'x-ms-blob-type: BlockBlob',
            'x-ms-date: ' . $currentDate,
            'x-ms-version: 2015-12-11',
        ];

        $ch = curl_init($destinationURL);


        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        echo ('Result<br/>');
        print_r($result);

        echo ('Error<br/>');
        print_r(curl_error($ch));

        curl_close($ch);
    }

    public function uploadDocuments(){

        $temp_file_location = storage_path().'/app/public/temp_pdf/';

        $files = scandir($temp_file_location);

        $file_names = array();

        if(!empty($files)){
            foreach($files as $file_name){
                if(!in_array($file_name,array('.','..'))){
                    $file_names[] = $file_name;
                }
            }
        }

        if(!empty($file_names)){

            $fileToUpload = $temp_file_location.$file_name;

            $uploaded = $this->uploadBlob($fileToUpload,$file_name);

            if($uploaded == 1){
                unlink($fileToUpload);
            }

        }
    }
}
