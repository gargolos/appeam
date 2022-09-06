<?php


namespace App\Http\Controllers;

//require 'vendor/autoload.php';

use Illuminate\Http\Request;
use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

class CredentialsController extends Controller
{
    public function presigned(Request $request)
    {
        try{

            $s3Client = new Aws\S3\S3Client([
                'profile' => 'default',
                'region' => 'us-east-2',
                'version' => '2006-03-01',
            ]);
            
            $cmd = $s3Client->getCommand('GetObject', [
                'Bucket' => 'my-bucket',
                'Key' => 'testKey'
            ]);
            
            $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
            $presignedUrl = (string)$request->getUri();
    
            $data =[
                'code' => 200,
                'status' => 'success',
                'presignedUrl' => $presignedUrl
            ];
            
            return response()->json($data, $data['code']);

        }catch (Throwable $e) {
            report($e);

            return false;
        }
    } 
}
