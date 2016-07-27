<?php
header("Content-type: image/jpeg");

include "config.inc";
require('S3.php');

function remoteFileExists($url) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    $result = curl_exec($curl);
    $ret = false;
    if ($result !== false) {
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode == 200) {
            $ret = true;
        }
    }
    curl_close($curl);
    return $ret;
}

$remote = $_GET["file"];
$filename = MD5($remote);

$exists = remoteFileExists("http://s3.amazonaws.com/".$s3_bucket."/".$filename.".jpg");
if ($exists) {
} else {
    $s3 = new S3($s3_key, $s3_secret);
    $grab = file_get_contents($remote);
    S3::putObject($grab,$s3_bucket,$filename.".jpg",S3::ACL_PUBLIC_READ,array(),array('Content-Type' => 'image/jpeg'),S3::STORAGE_CLASS_RRS);
}

imagejpeg(imagecreatefromjpeg("http://s3.amazonaws.com/".$s3_bucket."/".$filename.".jpg"));

?>
