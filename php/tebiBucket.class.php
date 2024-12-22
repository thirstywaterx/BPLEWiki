<?php
//require_once("/www/vendor/autoload.php");
require_once("vendor/autoload.php");
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
class tebiBucket{
    private $s3Client;
    public function createClient(){
        $this->s3Client = new Aws\S3\S3Client([
            "credentials" => [
                "key" => "",
                "secret" => ""
            ],
            "endpoint" => "https://s3.tebi.io",
            "region" => "de",
            "version" => "2006-03-01"
        ]);
    }
    public function bucketsName(){
        $buckets = $this->s3Client->listBuckets();
        $str="";
        foreach($buckets["Buckets"] as $b) {
            $str.=$b["Name"]." ";
        }
        return $str;
    }
    public function bucketcontent($bucket=""){
        $objs = $this->s3Client->listObjects(['Bucket' => $bucket]);
        return $objs["Contents"];
    }
    public function upload($filename,$filepath,$bucket=""){
        return $this->s3Client->putObject([
            'Bucket' => $bucket,
            'Key' => $filename,
            'SourceFile' => $filepath,
        ])["@metadata"]["statusCode"];
    }
    
    public function deleteFile($filename, $bucket = ""){
        return $this->s3Client->deleteObject([
            'Bucket' => $bucket,
            'Key' => "savefiles/" . $filename,
        ])["@metadata"]["statusCode"];
    }
    
    public function getfile($bucket=""){
        $content=$this->bucketcontent($bucket);
        foreach($content as $o){
            echo $o;
        }
    }
    public static function checktype($type,$name){
        $fe=pathinfo($name, PATHINFO_EXTENSION);
        return ($type==="application/octet-stream" && (empty($fe) || $fe==="bak"));
    }
}