<?php


namespace App\Http\Controllers;
require_once '../vendor/autoload.php';


use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

class CredentialsController extends Controller
{

    public function awsprueba()
    {
        $bucket_name ='bucketdeneto';
        $file_name ='NET.png';
        $s3client = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-1',
                'credentials' => [
                'key'      => 'AKIA3LBDZBPTF4TSAWJ6',
                'secret'   => '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',
            ]
        ]);

    }

    public function bucketcontent()
    {
 
        $bucket_name ='bucket-app-ppeam-images';
        $file_name ='NET.png';
        $s3client = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-1',
                'credentials' => [
                'key'      => 'AKIAYK7V3K5IO65DUQMJ',
                'secret'   => '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',
            ]
        ]);

        try {
            $contents = $s3client->listObjects([
                'Bucket' => $bucket_name,
            ]);

            echo "The contents of your bucket are: \n";
            foreach ($contents['Contents'] as $content) {
                echo $content['Key'] . "\n";
            }
        } catch (Exception $exception) {
            echo "Failed to list objects in $bucket_name with error: " . $exception->getMessage();
            exit("Please fix error with listing objects before continuing.");
        }

/*
        echo "<h2>Hola Test</h2>";
        $data =[
            'code' => 200,
            'status' => 'success',
            'presignedUrl' => $result
        ];
        
        return response()->json($data, $data['code']);
*/
    }

    public function presigned(Request $request)
    {

        $bucket_name ='bucket-app-ppeam-images';
        $s3client = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-1',
               'credentials' => [
                'key'      => 'AKIAYK7V3K5IO65DUQMJ',
                'secret'   => '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',
            ]
            
        ]);

      
            $cmd = $s3client->getCommand('GetObject', [
                'Bucket' => $bucket_name,
                'Key' => 'AKIAYK7V3K5IO65DUQMJ'
            ]);
            
            $request = $s3client->createPresignedRequest($cmd, '+20 minutes');
            
            $presignedUrl = (string)$request->getUri();
    
            $data =[
                'code' => 200,
                'status' => 'success',
                'presignedUrl' => $presignedUrl
            ];
            
            return response()->json($data, $data['code']);

    } 
}
