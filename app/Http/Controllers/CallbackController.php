<?php
/**
 * Description :
 * User        : liang
 * Date        : 17/2/21 上午10:11
 * Author      : wuliang
 */

namespace App\Http\Controllers;

use Log;
use Qiniu\Auth;
use Illuminate\Http\Request;
use Qiniu\Storage\UploadManager;

class CallbackController extends Controller
{
    public function callback(Request $request)
    {
        $result = $request->all();
        if (isset($result['type']) && $result['type'] == 1) {
            Log::info('request1:'.var_export($request->all(),1));
            Log::info('file:'.var_export($_FILES, 1));
            $file = $_FILES['upfile'];
            $path = storage_path().'/images'.$file['name'];
            Log::info('tmpname:'.var_export($file['tmp_name'],1));
            Log::info('path:'.var_export($path,1));
            if($res = move_uploaded_file($file['tmp_name'],$path)){
                Log::info('res:'.var_export($res,1));
                echo "Successfully!";
            }else{
                echo "Failed!";
            }
            exit;
           // return response($result);
        }
        // 用于签名的公钥和私钥
        $accessKey = env('QINIU_AXXESS_KEY');
        $secretKey = env('QINIU_SECRET_KEY');
        // 初始化签权对象
        $auth = new Auth($accessKey, $secretKey);
        $callbackBody = file_get_contents('php://input');//获取回调的body信息
        $contentType = 'application/x-www-form-urlencoded';
        //获取http头部的authorization 这里不同的服务器采用不同的方法来获取http头部
        if (strstr($request->server('SERVER_SOFTWARE'),"Apache")) {
            $data=apache_request_headers();
            $authorization = $data['Authorization'];
        } else {
            $authorization = $request->server('HTTP_AUTHORIZATION');
        }
        $url = 'http://123.56.220.231';
        $isQiniuCallback = $auth->verifyCallback($contentType, $authorization, $url, $callbackBody);
        if ($isQiniuCallback) {
            return $this->handleData($request->all());
            //$resp = array('ret' => 'this is test');
        } else {
            $resp = array('ret' => 'failed');
        }
        return response()->json($resp);
    }

    public function handleData($data)
    {
        $url = 'http://123.56.220.231';
        Log::info('data:'.var_export($data, 1));
        $downloadUrl = env('QINIU_DOMAINS_DEFAULT').'/'.$data['filename'];
        Log::info('downloadUrl:'.$downloadUrl);
        $fileinfo = explode('/',$data['filename']);
        Log::info('fileinfo:'.var_export($fileinfo, 1));
        $filePath = storage_path().'/'.$fileinfo[2];
        Log::info('filePath:'.$filePath);
        file_put_contents($filePath,'http://'.$downloadUrl);
        return $ret = $this->upload_file($url,$data['filename']);
        //$ret = $this->upload_file($url,'http://'.$downloadUrl);
        //Log::info('result:'.var_export($ret,1));
    }
    
    public function upload_file($url,$filename){
        $fileinfo = explode('/',$filename);
        $file = realpath(storage_path().'/'.$fileinfo[2]);
        $fields= new \CURLFile(realpath($file));
        $post_data = array(
            'type' => 1,
            'upfile'=> $fields,//绝对路径
        );
        Log::info("realpath:".var_export($post_data,1));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data );
        curl_exec( $ch );
        if ($error = curl_errno($ch)) {
            Log::info('errorsCode:'.var_export($error,1));
            Log::info('errorsInfo:'.var_export(curl_error($ch),1));
        }
        $return_data = curl_exec($ch);
        curl_close($ch);
        Log::info('return_data:'.$return_data);
        return $return_data;
    }
}
