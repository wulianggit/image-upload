<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
//
//$app->post('/', function (\Illuminate\Http\Request $request) {
//        $url =  "http://qiniu.upload.app/test";
//        $data['filename'] = 'Data/shop/000c08db43a29577564567d249c923b0.png';
//        $downloadUrl = env('QINIU_DOMAINS_DEFAULT').'/'.$data['filename'];
//        Log::info('downloadUrl:'.$downloadUrl);
//        $fileinfo = explode('/',$data['filename']);
//        Log::info('fileinfo:'.var_export($fileinfo, 1));
//        $filePath = storage_path().'/'.$fileinfo[2];
//        Log::info('filePath:'.$filePath);
//        $curl = curl_init($downloadUrl);
//        //$filename = date("Ymdhis").".jpg";
//        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
//        $imageData = curl_exec($curl);
//        curl_close($curl);
//        $tp = @fopen($filePath, 'a');
//        fwrite($tp, $imageData);
//        fclose($tp);
//        $file = realpath($filePath);
//        $fields= new \CURLFile(realpath($file));
//        $post_data = array(
//            'id' => 0,
//            'type' => 1,
//            'aucode' => "boqii",
//            'subtype' => 'article',
//            'method' => 'ajax',
//            'upfile'=> $fields,//绝对路径
//        );
//        Log::info("realpath:".var_export($post_data,1));
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url );
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_POST, 1 );
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data );
//        curl_exec( $ch );
//        if ($error = curl_errno($ch)) {
//            Log::info('errorsCode:'.var_export($error,1));
//            Log::info('errorsInfo:'.var_export(curl_error($ch),1));
//        }
//        $return_data = curl_exec($ch);
//        curl_close($ch);
//        Log::info('return_data:'.$return_data);
//        return $return_data;
//});
//
//$app->post('/test', function (\Illuminate\Http\Request $request) {
//    dd(getimagesize($_FILES['upfile']['tmp_name']));
//});

$app->post('/', 'CallbackController@callback');

$app->get('/upload', 'ImageController@testUpload');
$app->post('/moveImage', 'MoveImageController@moveImage');


$app->get('/storage', 'StorageController@upload');
